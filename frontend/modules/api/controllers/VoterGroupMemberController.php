<?php

class VoterGroupMemberController extends RestController {

    protected $convertRestFilters = true;
    
    public $acceptFilters = array(
        'plain' => 'first_name, last_name, birth_place, birth_day, gender, email',
    );    
    
    public $nestedModels = array(
        'profile' => array(
            'select' => 
                'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day, gender, email',
        )
    );
    
    public function getOutputFormatters() 
    {
        return array(
            'profile.birth_day' => array('Formatter', 'toTs')
        );
    }

//    public function doRestList() 
//    {
//        
//        $model = $this->getModel();
//        
//        if(empty($this->plainFilter['voter_group_id']))
//            throw new Exception ('VoterGroup id filter property missed');
//        
//        $group = VoterGroup::model()->findByPk($this->plainFilter['voter_group_id']);
//        
//        if(!$group)
//            throw new Exception('VoterGroup was not found');
//        
//        $criteria = new CDbCriteria(array(
//            'condition' => 't.voter_group_id = ' . $group->id
//        ));
//        
//        $peopleSearch = new PeopleSearch();
//        
//        if($name = $this->plainFilter['name'])
//            $peopleSearch->name = $name;
//        
//        if($ageFrom = $this->plainFilter['ageFrom'])
//            $peopleSearch->ageFrom = $ageFrom;
//        
//        if($ageTo = $this->plainFilter['ageTo'])
//            $peopleSearch->ageTo = $ageTo;
//        
//        if($birth_place = $this->plainFilter['birth_place'])
//            $peopleSearch->birth_place = $birth_place;
//        
//        if($gender = $this->plainFilter['gender'])
//            $peopleSearch->gender = $gender;
//        
//        $arProvCriteria = $peopleSearch->search('p')->criteria;
//        if($arProvCriteria)
//            $criteria->mergeWith($arProvCriteria);
//        
//        $results = $model->with($this->nestedRelations)
//            ->filter($this->restFilter)
//            ->orderBy($this->restSort)
//            ->limit($this->restLimit)
//            ->offset($this->restOffset)
//            ->findAll($criteria);
//        
//        $forCount = $this->getModel()->filter($this->restFilter);
//        
//        $this->outputHelper( 
//                'Records Retrieved Successfully', 
//                $results,
//                $forCount->with($this->nestedRelations)->count($criteria)
//        );
//    }    
    
    public function accessRules() {
        // @TODO Write normal accessRules
        return array(
            array('allow',
                'actions' => array('restList'),
                'users'=>array('*')
            ),
            array('allow',
                'actions'=>array('restCreate', 'restDelete', 'restUpdate'),
                'users'=>array('@')
            ),
            array(
                'deny',
                'actions' => array('restCreate', 'restDelete', 'restUpdate'),
                'users'=>array('*')
            )
        );
    }

}
