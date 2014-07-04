<?php
/**
 * Elector controller
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
Yii::import('frontend.modules.userAccount.models.Profile');

class ElectorController extends RestController {
    
    public $nestedModels = array(
        'profile' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day'
         )
    );

    public $acceptFilters = array(
        'plain' => 'name,election_id,ageFrom,ageTo,birth_place,gender'
    );
    
    public $virtualAttrs = array(
        'first_name', 'last_name', 'photo', 'photo_thmbnl_64', 'birth_place', 'birth_day', 'displayName'
    );
    
    public function getOutputFormatters() {
        return array(
            'profile.birth_day' => array('Formatter', 'toTs')
        );
    } 
    
    public function doRestList() {
        
        $model = $this->getModel();
        
        if(empty($this->plainFilter['election_id']))
            throw new Exception ('Election id filter property missed');
        
        $election = Election::model()->findByPk($this->plainFilter['election_id']);
        
        if(!$election)
            throw new Exception('Election was not found');
        
        $criteria = new CDbCriteria(array(
            'condition' => 't.election_id = ' . $election->id
        ));
        
        $peopleSearch = new PeopleSearch();
        
        if($name = $this->plainFilter['name'])
            $peopleSearch->name = $name;
        
        if($ageFrom = $this->plainFilter['ageFrom'])
            $peopleSearch->ageFrom = $ageFrom;
        
        if($ageTo = $this->plainFilter['ageTo'])
            $peopleSearch->ageTo = $ageTo;
        
        if($birth_place = $this->plainFilter['birth_place'])
            $peopleSearch->birth_place = $birth_place;
        
        if($gender = $this->plainFilter['gender'])
            $peopleSearch->gender = $gender;
        
        $arProvCriteria = $peopleSearch->search('')->criteria;
        if($arProvCriteria)
            $criteria->mergeWith($arProvCriteria);
        
        $results = $model->with($this->nestedRelations)
            ->orderBy($this->restSort)
            ->limit($this->restLimit)->offset($this->restOffset)
            ->findAll($criteria);
        
        $forCount = $this->getModel();
        
        $this->outputHelper( 
                'Records Retrieved Successfully', 
                $results,
                $forCount->with($this->nestedRelations)->count($criteria)
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('restList'),
                'users'=>array('*')
            ),
            array('allow',
                'actions' => array('restCreate', 'restDelete'),
                'users'=>array('@'),
                'expression' => array($this, 'checkAccess')
            ),
            array('deny',
                'actions'=>array('restCreate', 'restDelete', 'restUpdate'),
                'users'=>array('*')
            )
        );
    }
    
    public function checkAccess() {
        if(!empty($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadOneModel((int)$id);

            if(!$model)
                throw new Exception ('Elector with id = ' . $id . ' was not found');

            $election = $model->election;
            
        } else {
            $data = $this->data();
            $election = Election::model()->findByPk((int)$data['election_id']);
            $params['elector_user_id'] = $data['user_id'];
        }
        
        if(!$election)
            throw new Exception('Related Election can\'t be fetched');
        
        $params['election'] = $election;
        
        if( $this->action->id == 'restCreate' && Yii::app()->user->checkAccess('election_addElector', $params) )
            return true;  
        
        if( $this->action->id == 'restDelete' && Yii::app()->user->checkAccess('election_manage', $params) )
            return true;
        
        return false;
    }    
}
