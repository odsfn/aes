<?php

class ElectionController extends RestController {

    protected $convertRestFilters = true;
    
    public $nestedModels = array();
    
    public $acceptFilters = array('plain' => 'voter_id', 'model' => 'name,status');

    public $outFormatters = array(
        'candidates.votes.date' => array('Formatter', 'toTs')
    );

    public function getOutputFormatters() {
        return $this->outFormatters;
    }
    
    public function onPlainFilter_voter_id($filterName, $filterValue, $criteria) {
        $this->nestedModels = array(
            
            'candidates.profile' => array(
                'select' => 'user_id, first_name, last_name'
            ),
            
            'candidates.votes' => array(
                'joinType' => 'INNER JOIN',
                'on'       => 'votes.election_id = t.id AND votes.user_id = :voterId',
                'order'    => 'votes.date DESC',
                'params' => array(':voterId' => $filterValue),
            ),
            
            'candidates.votes.rates', 'candidates.votes.positiveRatesCount',
            'candidates.votes.negativeRatesCount',
            
            'candidates.votes.comments', 'candidates.votes.commentsCount', 
            'candidates.votes.comments.user' => array(
                'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
            ),
            'candidates.votes.comments.rates', 'candidates.votes.comments.positiveRatesCount', 
            'candidates.votes.comments.negativeRatesCount'
        );
        
        $criteria->mergeWith(array(
            'join' => 'INNER JOIN vote ON vote.election_id = t.id AND vote.user_id = :voterId',
            'params' => array(':voterId' => $filterValue)
        ));
        
        $this->outFormatters = array_merge($this->outFormatters, array(
            'candidates.votes.comments.created_ts' => array('Formatter', 'toTs'),
            'candidates.votes.comments.last_update_ts' => array('Formatter', 'toTs'),
            'candidates.votes.comments.rates.created_ts' => array('Formatter', 'toTs')            
        ));
    }
    
    protected function getResultsCount($criteria)
    {
        if($this->plainFilter['voter_id']) {
            return Yii::app()->db->createCommand('SELECT COUNT(id) FROM vote '
                    . 'WHERE user_id = ' . $this->plainFilter['voter_id']
                   )->queryScalar();
        }
        
        return $this->getModel()
                        ->with($this->nestedRelations)
                        ->filter($this->restFilter)
                        ->count($criteria);
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
