<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class CandidateController extends RestController {
    
    public $acceptFilters = array(
        'plain' => 'election_id,name,status'
    );    
    
    public $nestedModels = array(
        'profile' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day'
        )
    );
    
    public $virtualAttrs = array(
        'profile'
    );

    public function getOutputFormatters() {
        return array(
            'profile.birth_day' => array('Formatter', 'toTs')
        );
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
        
        if(!empty($this->plainFilter['name']))
            $criteria->mergeWith(PeopleSearch::getCriteriaFindByName($this->plainFilter['name'], 'profile'));
        
        if(!empty($this->plainFilter['status']))
            $criteria->mergeWith(Candidate::getCriteriaWithStatusOnly($this->plainFilter['status']));
        
        $results = $this->getModel()
                ->with($this->nestedRelations)
                ->limit($this->restLimit)
                ->offset($this->restOffset)
        ->findAll($criteria);
        
        $this->outputHelper( 
            'Records Retrieved Successfully',
            $results,
            $this->getModel()
                    ->with($this->nestedRelations)
            ->count($criteria)
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
        
        Yii::app()->authManager->defaultRoles = array_merge(Yii::app()->authManager->defaultRoles, array('election_updateCandidateOwnStatus'));
        
        $data = $this->data();
        
        if(!empty($_GET['id'])) {
            
            $id = $_GET['id'];
            $model = $this->loadOneModel((int)$id);

            if(!$model)
                throw new Exception ('Candidate with id = ' . $id . ' was not found');

            $election = $model->election;
            
        } else {
            $election = Election::model()->findByPk((int)$data['election_id']);
            $params['candidate_user_id'] = $data['user_id'];
        }
        
        if(!$election)
            throw new Exception('Related Election can\'t be fetched');
        
        $params['election'] = $election;
        if($model) {
            $params['candidate'] = $model;
            
            if(isset($data['status']))
                $params['status'] = $data['status'];
        }
        
        if( $this->action->id == 'restCreate' && Yii::app()->user->checkAccess('election_createCandidate', $params) )
            return true;
        
        if( $this->action->id == 'restDelete' && Yii::app()->user->checkAccess('election_deleteCandidate', $params) )
            return true;
  
        if( $this->action->id == 'restUpdate' && Yii::app()->user->checkAccess('election_updateCandidateStatus', $params) )
            return true;
        
        return false;
    }
}
