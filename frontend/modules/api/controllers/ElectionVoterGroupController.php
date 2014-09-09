<?php

class ElectionVoterGroupController extends RestController {

    public $nestedModels = array(
        
    );
    
    protected $convertRestFilters = true;

//    public $acceptFilters = array('plain' => 'creator_name,support,creation_date', 'model' => 'title, mandate_id, creator_id');

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
