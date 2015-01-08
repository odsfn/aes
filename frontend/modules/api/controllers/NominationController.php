<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
Yii::import('frontend.modules.api.controllers.CandidateController');

class NominationController extends CandidateController
{
    public $acceptFilters = array(
        'plain' => 'name,status,user_id'
    );    
    
    public $nestedModels = array(
        
        'profile' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day'
        ),
        
        'election' => array(
            'select' => 'name, status'
        ),
        
        'rates', 'positiveRatesCount', 'negativeRatesCount',
    );
    
    public $virtualAttrs = array(
        'profile', 'election'
    );
    
    public function getOutputFormatters() {
        return array(
            'profile.birth_day' => array('Formatter', 'toTs'),
            'status_changed_ts' => array('Formatter', 'toTs')
        );
    }    
    
    public function createModel()
    {
        $this->model = new Candidate();
        
        return $this->model;
    }
    
    public function doRestList() {
        
        $criteria = new CDbCriteria(array('condition' => 't.user_id = ' . $this->plainFilter['user_id']));
        
        if(!empty($this->plainFilter['name']))
            $criteria->mergeWith(array(
                'condition' => 'election.name LIKE :electionName',
                'params' => array(':electionName' => '%' . $this->plainFilter['name'] . '%')
            ));
        
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
}
