<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class VoteController extends RestController {
    
    public $nestedModels = array();
    
    public $acceptFilters = array(
        'plain' => 'election_id,user_id'
    );
    
    public function getOutputFormatters() {
        return array(
            'date' => array('Formatter', 'toTs')
        );
    }
    
    public function doRestCreate($data) {
        $candidate = Candidate::model()->findByPk($data['candidate_id']);
        
        if(!$candidate)
            throw new Exception('Candidate was not found');
        
        $data['election_id'] = $candidate->election_id;
        
        parent::doRestCreate($data);
    }
    
    public function doRestList() {
        
        if(empty($this->plainFilter['election_id']))
            throw new Exception ('Election id filter property missed');
        
        $election = Election::model()->findByPk($this->plainFilter['election_id']);
        
        if(!$election)
            throw new Exception('Election was not found');
        
        $criteria = new CDbCriteria(array(
            'condition' => 't.election_id = ' . $election->id
        ));
        
        if(isset($this->plainFilter['user_id']))
            $criteria->mergeWith(array('condition' => 't.user_id = ' . (int)$this->plainFilter['user_id']));
        
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
    
    public function accessRules() {
	return array(
            array('allow',
                'actions' => array('restCreate', 'restUpdate', 'restDelete'),
                'users' => array('@'),
//                'expression' => array($this, 'checkAccess')
            ),
	    array('deny', 
		'actions' => array('restCreate', 'restDelete', 'restUpdate'),
		'users' => array('*')
	    )
	);
    }    
}
