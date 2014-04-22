<?php

class PetitionRateController extends RestController {

    public $nestedModels = array(
        'mandate.candidate.profile' => array(
            'select' => 'profile.user_id, profile.first_name, profile.last_name'
        ),
        'creator' => array(
            'select' => 'creator.user_id, creator.first_name, creator.last_name'
        )
    );
    
    public $acceptFilters = array('plain' => 'owner_name,user_id', 'model' => 'name,status');

    public function getOutputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'toTs')
        );
    }
    
//    public function onPlainFilter_owner_name($filterName, $filterValue, $criteria) {        
//        $criteria->mergeWith(PeopleSearch::getCriteriaFindByName($filterValue, 'profile'));
//    }
//    
//    public function onPlainFilter_user_id($filterName, $filterValue, $criteria) {        
//        $criteria->mergeWith(array(
//            'join' => 'INNER JOIN candidate c ON c.user_id = :userId AND t.candidate_id = c.id',
//            'params' => array(':userId' => $filterValue)
//        ));
//    }
    
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
