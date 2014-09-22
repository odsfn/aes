<?php

class VoterGroupController extends RestController {

    public $acceptFilters = array('plain' => 'electionScope');
    
    public $nestedModels = array(
        
    );
    
    protected $convertRestFilters = true;

    /**
     * Scope for management of voter groups in context of election by election's
     * manager
     */
    public function onPlainFilter_electionScope($filterName, $filterValue, $criteria)
    {
        $election_id = $filterValue['election_id'];
        
        $criteria->mergeWith(array(
            'condition' => 
                "(type = " . VoterGroup::TYPE_LOCAL . " AND election_id = $election_id)"
                . " OR type= " . VoterGroup::TYPE_GLOBAL
        ));
        
        $this->flushRestFilter($filterName);
    }

    public function accessRules() {
        // @TODO Write normal accessRules
        // create, delete, update local - election_admin of related election
        // create, delete, update global - superadmin
        return array(
            array('allow',
                'actions' => array('restList'),
                'users'=>array('*')
            ),
            array('allow',
                'actions'=>array('restCreate', 'restDelete', 'restUpdate'),
                'users' => array('@'),
                'expression' => array($this, 'checkAccess')
            ),
            array(
                'deny',
                'actions' => array('restCreate', 'restDelete', 'restUpdate'),
                'users'=>array('*')
            )
        );
    }

    public function checkAccess()
    {
        if (Yii::app()->user->checkAccess('superadmin') ) {
            return true;
        }
        
        if ( in_array($this->action->id, array('restDelete', 'restUpdate')) ) {
            $id = (int) $_GET['id'];
            $group = $this->loadOneModel($id);
            
            if (!$group) {
                throw new CException(
                    'VoterGroup with id "' . $id . '" was not found'
                );
            }
            
            if ( $group->type == VoterGroup::TYPE_LOCAL ) {
                $params = array('election' => $group->election);
                return Yii::app()->user->checkAccess('election_administration', $params);
            }
            
            return false;
        }
        
        if ( $this->action->id == 'restCreate' ) {
            
            $data = $this->data();
            $type = $data['type'];
                    
            if ($type == VoterGroup::TYPE_LOCAL) {
                
                $election = Election::model()->findByPk((int)$data['election_id']);
                
                if (!$election) {
                    throw new CException(
                        'Election with id "' .$data['election_id'] . '" was not found'
                    );
                }
                
                $params = array('election' => $election);
                return Yii::app()->user->checkAccess('election_administration', $params);
            }
            
            return false;
        }
    }
}
