<?php

class MandateController extends RestController {

    public $nestedModels = array(
        'candidate.profile' => array(
            'select' => 'profile.user_id, profile.first_name, profile.last_name'
        ),
        'election' => array(
            'select' => 'name'
        ),
        'rates', 'positiveRatesCount', 'negativeRatesCount',
    );
    
    public $outFormatters = array(
        'submiting_ts' => array('Formatter', 'toTs'),
        'expiration_ts' => array('Formatter', 'toTs'),
        'rates.created_ts' => array('Formatter', 'toTs'),
    );

    public $acceptFilters = array('plain' => 'owner_name,user_id', 'model' => 'name,status, election_id');

    public function getOutputFormatters() {
        return $this->outFormatters;
    }
    
    public function onPlainFilter_owner_name($filterName, $filterValue, $criteria) {        
        $criteria->mergeWith(PeopleSearch::getCriteriaFindByName($filterValue, 'profile'));
    }
    
    public function onPlainFilter_user_id($filterName, $filterValue, $criteria) {        
        $criteria->mergeWith(array(
            'join' => 'INNER JOIN candidate c ON c.user_id = :userId AND t.candidate_id = c.id',
            'params' => array(':userId' => $filterValue)
        ));
        
        $this->nestedModels = array_merge($this->nestedModels, array(
            'comments', 'commentsCount', 
            'comments.user' => array(
                'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
            ),
            'comments.rates', 'comments.positiveRatesCount', 'comments.negativeRatesCount'
        ));
        
        $this->outFormatters = array_merge($this->outFormatters, array(
            'comments.created_ts' => array('Formatter', 'toTs'),
            'comments.last_update_ts' => array('Formatter', 'toTs'),
            'comments.rates.created_ts' => array('Formatter', 'toTs')
        ));
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
