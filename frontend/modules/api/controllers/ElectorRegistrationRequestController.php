<?php

/**
 * ElectorRegistrationRequest rest controller
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
Yii::import('frontend.modules.userAccount.models.Profile');

class ElectorRegistrationRequestController extends RestController
{
    protected $convertRestFilters = true;

    public $nestedModels = array(
        'profile' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day'
        ),
        'elector'
    );

    public function getOutputFormatters()
    {
        return array(
            'user.birth_day' => array('Formatter', 'toTs')
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('restList'),
                'users' => array('*')
            ),
            array('allow',
                'actions' => array('restCreate', 'restUpdate'),
                'users' => array('@'),
                'expression' => array($this, 'checkAccess')
            ),
            array('deny',
                'actions' => array('restCreate', 'restDelete', 'restUpdate'),
                'users' => array('*')
            )
        );
    }

    public function checkAccess()
    {
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadOneModel((int) $id);

            if (!$model)
                throw new Exception('ElectorRegistrationRequest with id = ' . $id . ' was not found');

            $election = $model->election;
        } else {
            $data = $this->data();
            $election = Election::model()->findByPk((int) $data['election_id']);
        }

        if (!$election)
            throw new Exception('Related Election can\'t be fetched');

        $params['election'] = $election;

        if ($this->action->id == 'restCreate' && Yii::app()->user->checkAccess('election_askToBecameElector', $params))
            return true;

        if ($this->action->id == 'restUpdate' && Yii::app()->user->checkAccess('election_manage', $params))
            return true;

        return false;
    }

}
