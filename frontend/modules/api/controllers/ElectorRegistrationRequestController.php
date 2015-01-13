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
        'elector' => array(
            'select' => '*'
        )
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

    public function _filters()
    {
        return array(
            'checkExists + restCreate',
        );
    }
    
    public function filterCheckExists($filterChain)
    {
        $data = $this->data();
        
        $request = ElectorRegistrationRequest::model()
            ->with($this->nestedModels)
            ->findByAttributes(array(
                'user_id' => (int)$data['user_id'],
                'election_id' => (int)$data['election_id']
            ));
        
        if($request) {
            
            if($request->user_id == Yii::app()->user->id) {
                switch ($request->status) {
                    case ElectorRegistrationRequest::STATUS_REGISTERED:
                        $message = 'You have been already registered as elector';
                        break;
                    default:
                        $message = 'You have already requested permission to become elector. '
                            . 'Election administrator will consider it soon.';
                        break;
                }
            } else {
                $message = 'Elector registration request has been already created';
            }
            
            $this->renderJson(array(
                'success'=>true,
                'status'=>'exists',
                'message'=>Yii::t('aes', $message),
                'data' => array(
                    'totalCount' => 1,
                    'models'=>$this->allToArray(array($request))
                )
            ));
            
            return true;
        } else {
            $elector = Elector::model()->find(
                'user_id = :userId AND election_id = :electionId', 
                array(
                    ':userId' => $data['user_id'], 
                    ':electionId' => $data['election_id']
                )
            );
            
            if($elector) {
                $this->renderJson(array(
                    'success'=>true,
                    'status'=>'exists_elector',
                    'message'=>Yii::t('aes', 'You have been already registered as elector')
                ));
                return true;
            }
        }
        
        $filterChain->run();
    }
}
