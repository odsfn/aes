<?php

class VoterGroupController extends RestController {

    public $acceptFilters = array('plain' => 'electionScope');
    
    public $nestedModels = array(
        
    );
    
    protected $convertRestFilters = true;

    /**
     * Scope for management of voter groups in context of election by election's
     * manager
     */
    public function onPlainFilter_electionScope($filterName, $filterValue, $criteria)
    {
        $election_id = $filterValue['election_id'];
        
        $criteria->mergeWith(array(
            'condition' => 
                "(type = " . VoterGroup::TYPE_LOCAL . " AND election_id = $election_id)"
                . " OR type= " . VoterGroup::TYPE_GLOBAL
        ));
        
        $this->flushRestFilter($filterName);
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
