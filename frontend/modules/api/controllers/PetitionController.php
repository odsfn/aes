<?php

class PetitionController extends RestController {

    public $nestedModels = array(
        'mandate.candidate.profile' => array(
            'select' => 'profile.user_id, profile.first_name, profile.last_name'
        ),
        'creator' => array(
            'select' => 'creator.user_id, creator.first_name, creator.last_name'
        ),
        'rates', 'positiveRatesCount', 'negativeRatesCount'
    );
    
    protected $convertRestFilters = true;

    public $acceptFilters = array('plain' => 'creator_name,support,creation_date', 'model' => 'title, mandate_id, creator_id');

    public function getOutputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'toTs')
        );
    }
    
    public function onPlainFilter_creator_name($filterName, $filterValue, $criteria) {        
        $criteria->mergeWith(PeopleSearch::getCriteriaFindByName($filterValue, 'creator'));
    }
    
    public function onPlainFilter_support($filterName, $filterValue, $criteria) {
        
        $userId = Yii::app()->user->id;
        
        if(!$userId)
            return;
        
        if($filterValue === 'created_by_user')
            $criteria->mergeWith(array('condition' => 'creator_id = ' . $userId));
        elseif($filterValue === 'supported_by_user')
            $criteria->mergeWith(array('join' => 'INNER JOIN petition_rate pr ON pr.target_id = t.id AND user_id = ' . $userId));
    }
    
    public function onPlainFilter_creation_date($filterName, $filterValue, $criteria) {
        
        $curDate = new DateTime;
        
        if($filterValue === 'today')
            $condition = 'created_ts >= "' . $curDate->format('Y-m-d') . '"';
        elseif($filterValue === 'week') {
            $condition = 'created_ts >= "' . $curDate->sub(new DateInterval('P1W'))->format('Y-m-d') . '"';
        } elseif ($filterValue === 'month')
            $condition = 'created_ts >= "' . $curDate->sub(new DateInterval('P1M'))->format('Y-m-d') . '"';
        else
            return;
        
        $criteria->mergeWith(array('condition' => $condition));
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
