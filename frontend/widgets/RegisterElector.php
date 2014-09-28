<?php

/** 
 * Widget for rendering of Register Elector button and corresponding code
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class RegisterElector extends CWidget
{
    /**
     * Election to register in
     * 
     * @var Election 
     */
    public $election;
    
    public function run()
    {
        if (!Yii::app()->user->checkAccess('election_askToBecameElector', 
            array(
                'election' => $this->election
            )
        )) return;
        
        $this->registerScripts();
        
        $this->render('registerElector');
    }
    
    protected function registerScripts()
    {
        $cs = Yii::app()->clientScript->registerPackage('aes-common')
            ->registerPackage('loadmask')
            ->registerScriptFile('/js/libs/aes/models/User.js')
            ->registerScriptFile('/js/libs/aes/models/ElectorRegistrationRequest.js')
            ->registerScriptFile('/js/libs/aes/views/ItemView.js')
            ->registerScriptFile('/js/libs/aes/views/ModalView.js')
            ->registerScriptFile('/js/libs/aes/views/ButtonView.js')
            ->registerScriptFile('/js/libs/aes/views/NotificationsView.js');
    }
}
