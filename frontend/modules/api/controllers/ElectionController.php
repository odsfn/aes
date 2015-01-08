<?php

class ElectionController extends RestController {

    protected $convertRestFilters = true;
    
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
                'order'    => 'votes.date DESC',
                'params' => array(':voterId' => $filterValue),
            ),
            
            'candidates.votes.rates', 'candidates.votes.positiveRatesCount',
            'candidates.votes.negativeRatesCount',
            
        );
        
        $criteria->mergeWith(array(
            'join' => 'INNER JOIN vote ON vote.election_id = t.id AND vote.user_id = :voterId',
            'params' => array(':voterId' => $filterValue)
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
