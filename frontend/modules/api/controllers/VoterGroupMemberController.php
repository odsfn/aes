<?php

class VoterGroupMemberController extends RestController {

    protected $convertRestFilters = true;
    
    public $acceptFilters = array(
        'plain' => 'first_name, last_name, birth_place, birth_day, gender, email',
    );    
    
    public $nestedModels = array(
        'profile' => array(
            'select' => 
                'user_id, profile.first_name AS first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day, gender, email'
        )
    );
    
    public function getOutputFormatters() 
    {
        return array(
            'profile.birth_day' => array('Formatter', 'toTs')
        );
    }
    
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
