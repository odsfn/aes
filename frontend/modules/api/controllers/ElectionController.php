<?php

class ElectionController extends RestController {

    public $nestedModels = array();
    
    public $acceptFilters = array('plain' => 'voter_id', 'model' => 'name,status');

    public function getOutputFormatters() {
        return array(
            'candidates.votes.date' => array('Formatter', 'toTs')
        );
    }
    
    public function onPlainFilter_voter_id($filterName, $filterValue, $criteria) {
        $this->nestedModels = array(
            
            'candidates.profile' => array(
                'select' => 'user_id, first_name, last_name'
            ),
            
            'candidates.votes' => array(
                'joinType' => 'INNER JOIN',
                'on'       => 'votes.election_id = t.id AND votes.user_id = :voterId',
                'params' => array(':voterId' => $filterValue)
            )
            
        );
        
        $criteria->mergeWith(array(
            'join' => 'INNER JOIN vote ON vote.election_id = t.id AND vote.user_id = :voterId',
            'params' => array(':voterId' => $filterValue)
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
