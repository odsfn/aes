<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class VoteController extends RestController {
    
    public $nestedModels = array();
    
    public $acceptFilters = array(
        'plain' => 'election_id,user_id,candidate_id,with_profile,name'
    );
    
    public $virtualAttrs = array(
        'profile'
    );  
    
    public function getOutputFormatters() {
        return array(
            'date' => array('Formatter', 'toTs')
        );
    }
    
    public function doRestCreate($data) {
        $candidate = Candidate::model()->findByPk($data['candidate_id']);
        
        if(!$candidate)
            throw new Exception('Candidate was not found');
        
        $data['election_id'] = $candidate->election_id;
        
        parent::doRestCreate($data);
    }
    
    public function doRestList() {
        
        if(empty($this->plainFilter['election_id']))
            throw new Exception ('Election id filter property missed');
        
        $election = Election::model()->findByPk($this->plainFilter['election_id']);
        
        if(!$election)
            throw new Exception('Election was not found');
        
        $criteria = new CDbCriteria(array(
            'condition' => 't.election_id = ' . $election->id
        ));
        
        if(isset($this->plainFilter['user_id']))
            $criteria->mergeWith(array('condition' => 't.user_id = ' . (int)$this->plainFilter['user_id']));
        
        if(!empty($this->plainFilter['name']))
            $criteria->mergeWith(PeopleSearch::getCriteriaFindByName($this->plainFilter['name'], 'profile'));        
        
        if(!empty($this->plainFilter['with_profile']))
            $this->nestedModels = array(
                'profile' => array(
                    'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day'
                )                
            );
        
        if(isset($this->plainFilter['candidate_id']))
            $criteria->mergeWith(array('condition' => 't.candidate_id = ' . (int)$this->plainFilter['candidate_id']));
        
        $results = $this->getModel()
                ->with($this->nestedRelations)
                ->limit($this->restLimit)
                ->offset($this->restOffset)
        ->findAll($criteria);
        
        $totalCount = $this->getModel()
            ->with($this->nestedRelations)
            ->count($criteria);
        
        $extraData = $totalCount;
        
        if(isset($this->plainFilter['candidate_id'])) {
            $extraData = array(
                'totalCount'    => $totalCount,
                'declinedCount' => $this->getModel()
                    ->with($this->nestedRelations)
                    ->count($criteria->addCondition('t.status = ' . Vote::STATUS_DECLINED))
            );
        }
        
        $this->outputHelper( 
            'Records Retrieved Successfully',
            $results,
            $extraData
        );
    }
    
    public function accessRules() {
	return array(
            array('allow',
                'actions' => array('restCreate', 'restUpdate', 'restDelete'),
                'users' => array('@'),
                'expression' => array($this, 'checkAccess')
            ),
	    array('deny', 
		'actions' => array('restCreate', 'restDelete', 'restUpdate'),
		'users' => array('*')
	    )
	);
    }
    
    public function checkAccess() {
        
        Yii::app()->authManager->defaultRoles = 
                array_merge(
                        Yii::app()->authManager->defaultRoles, 
                        array('election_elector', 'election_updateVoteStatus')
                );
        
        $data = $this->data();
        
        if(!empty($_GET['id'])) {
            
            $id = $_GET['id'];
            $model = $this->loadOneModel((int)$id);

            if(!$model)
                throw new Exception ('Vote with id = ' . $id . ' was not found');
            
            $candidate = $model->candidate;
            $election = $candidate->election;
            
        } else {
            $candidate = Candidate::model()->findByPk($data['candidate_id']);
            $election = $candidate->election;
        }
        
        if(!$candidate)
            throw new Exception ('Related Candidate was not found');
        
        if(!$election)
            throw new Exception('Related Election was not found');
        
        $params['election'] = $election;
        $params['candidate'] = $candidate;
        if($model) {
            $params['vote'] = $model;
            
            if(isset($data['status']))
                $params['status'] = $data['status'];
        }
        /**
         * @TODO Provide access check to allow vote only electorate
         */
        if( $this->action->id == 'restCreate' && Yii::app()->user->checkAccess('election_createVote', $params) )
            return true;
        
        if( $this->action->id == 'restDelete' && Yii::app()->user->checkAccess('election_deleteVote', $params) )
            return true;
  
        if( $this->action->id == 'restUpdate' && Yii::app()->user->checkAccess('election_updateVoteStatus', $params) )
            return true;
        
        return false;
    }    
}
