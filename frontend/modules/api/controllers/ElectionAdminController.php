<?php
/**
 * 
 * @todo Provide strong access check
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionAdminController extends RestController {
    
    public $acceptFilters = array(
        'plain' => 'election_id,name'
    );

    public $nestedModels = array(
        'profile' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day'
        ),
        
        'authAssignment' => array(
            'select' => 'id, itemname, userid'
        )
    );

    public $virtualAttrs = array('right');
    
    public function createModel() {
        return new ElectionAuthAssignment();
    }

    public function getOutputFormatters() {
        return array(
            'profile.birth_day' => array('Formatter', 'toTs')
        );
    }
    
    public function doRestCreate($data) {
        $election = Election::model()->findByPk($data['object_id']);
        
        if(!$election)
            throw new Exception('Election was not found');
        
        $assignment = $election->assignRoleToUser($data['profile']['user_id'], 'election_admin');
        
        $result = $this->getModel()->with($this->nestedRelations)->findByPk($assignment->id);
        $models = array($result);
        
        $this->outputHelper(
            'Record(s) Created',
            $models,
            1
        );
    }

    public function doRestList() {
        
        if(empty($this->plainFilter['election_id']))
            throw new Exception ('Election id filter property missed');
        
        $election = Election::model()->findByPk($this->plainFilter['election_id']);
        
        if(!$election)
            throw new Exception('Election was not found');
        
        $criteria = new CDbCriteria(array(
            'condition' => 't.object_id = ' . $election->id
        ));
        
        if(!empty($this->plainFilter['name']))
            $criteria->mergeWith(PeopleSearch::getCriteriaFindByName($this->plainFilter['name'], 'profile'));
        
        $results = $this->getModel()
                ->with($this->nestedRelations)
                ->criteriaWithRole('election_administration')
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
                'actions' => array('restCreate', 'restDelete'),
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
        if(!empty($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadOneModel((int)$id);

            if(!$model)
                throw new Exception ('ElectionAuthAssignment with id = ' . $id . ' was not found');

            $election = $model->object;
            
        } else {
            $data = $this->data();
            $election = Election::model()->findByPk((int)$data['object_id']);
        }
        
        if(!$election)
            throw new Exception('Related Election can\'t be fetched');
        
        $params['election'] = $election;
        
        if( $this->action->id == 'restCreate' && Yii::app()->user->checkAccess('election_manageAdmins', $params) )
            return true;
        
        if( $this->action->id == 'restDelete' && Yii::app()->user->checkAccess('election_manageAdmins', $params) ) {
            if($model->right == 'election_creator')
                throw new Exception('Election creator can\'t be deprived of power.');
            return true;
        }
        
        return false;
    }    
}
