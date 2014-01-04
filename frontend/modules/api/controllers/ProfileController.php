<?php
/**
 * Profides listing for user's profiles
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
Yii::import('frontend.modules.userAccount.models.Profile');

class ProfileController extends RestController {
    
    public $nestedModels = array();

    public $acceptFilters = array(
        'plain' => 'name'
    );
    
    public function getOutputFormatters() {
        return array(
            'birth_day' => array('Formatter', 'toTs')
        );
    }    
    
    public function doRestList() {
        
        $model = $this->getModel();
        $model->activeOnly();
        $model->getDbCriteria()->mergeWith(PeopleSearch::getCriteriaFindByName($this->plainFilter['name']));
        $model->getDbCriteria()->select = 'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day';
        
        
        $results = $model->with($this->nestedRelations)
            ->orderBy($this->restSort)
            ->limit($this->restLimit)->offset($this->restOffset)
            ->findAll();
        
        $forCount = $this->getModel();
        $forCount->activeOnly();
        $forCount->getDbCriteria()
            ->mergeWith(PeopleSearch::getCriteriaFindByName($this->plainFilter['name']));
        
        $this->outputHelper( 
                'Records Retrieved Successfully', 
                $results,
                $forCount->with($this->nestedRelations)->count()
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('restList'),
                'users'=>array('*')
            ),
            array('deny',
                'actions'=>array('restCreate', 'restDelete', 'restUpdate'),
                'users'=>array('*')
            )
        );
    }
    
}
