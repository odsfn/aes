<?php

class VoterGroupMemberController extends RestController {

    protected $convertRestFilters = true;
    
    public $acceptFilters = array(
        'plain' => 'first_name, last_name, birth_place, birth_day, gender, email',
    );    
    
    public $nestedModels = array(
        'profile' => array(
            'select' => 
                'user_id, profile.first_name AS first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day, gender, email'
        )
    );
    
    public function getOutputFormatters() 
    {
        return array(
            'profile.birth_day' => array('Formatter', 'toTs')
        );
    }
    
    public function accessRules() {
        // @TODO Write normal accessRules
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
            $member = $this->loadOneModel($id);
            
            if (!$member) {
                throw new CException(
                    'Member with id "' . $id . "' was not found"
                );
            }
            
            $group = $member->voterGroup;
            
            if (!$group) {
                throw new CException(
                    'VoterGroup was not found'
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
            
            $group = VoterGroup::model()->findByPk($data['voter_group_id']);
            
            if (!$group) {
                throw new CException(
                    'VoterGroup was not found'
                );
            }            
            
            $type = $group->type;
                    
            if ($type == VoterGroup::TYPE_LOCAL) {
                
                $election = $group->election;
                
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