<?php

class ElectionVoterGroupController extends RestController {

    public $nestedModels = array(
        
    );
    
    protected $convertRestFilters = true;

    public function accessRules() {
        // @TODO Write normal accessRules
        return array(
            array('allow',
                'actions' => array('restList'),
                'users'=>array('*')
            ),
            array('allow',
                'actions'=>array('restCreate', 'restDelete', 'restUpdate'),
                'users'=>array('@'),
                'expression' => array($this, 'checkAccess')
            ),
            array(
                'deny',
                'actions' => array('restCreate', 'restDelete', 'restUpdate'),
                'users'=>array('*')
            )
        );
    }

    public function checkAccess()
    {
        if (isset($_GET['id'])) {
            $model = $this->loadOneModel($id = (int)$_GET['id']);
            $election = $model->election;
        } else {
            $data = $this->data();
            $election = Election::model()->findByPk((int)$data['election_id']);
        }
        
        if (!$election) {
            throw new CException('Related election was not found');
        }
        
        $params = array('election' => $election );
        return Yii::app()->user->checkAccess('election_administration', $params);
    }
}
