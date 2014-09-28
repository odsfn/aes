<?php

/** 
 * Represents Register Candidate button
 */
class RegisterCandidate extends CWidget
{
    /**
     * Election to register in
     * 
     * @var Election 
     */
    public $election;
    
    public function run()
    {
        if (!Yii::app()->user->checkAccess('election_selfAppointment', 
            array(
                'election' => $this->election,
                'candidate_user_id' => Yii::app()->user->id
        ))) return;
        
        $this->registerScripts();
        
        $this->render('registerCandidate');
    }
    
    protected function registerScripts()
    {
        $cs = Yii::app()->clientScript->registerPackage('aes-common')
            ->registerPackage('loadmask')
            ->registerScriptFile('/js/libs/aes/models/User.js')
            ->registerScriptFile('/js/libs/aes/models/Candidate.js')
            ->registerScriptFile('/js/libs/aes/views/ItemView.js')
            ->registerScriptFile('/js/libs/aes/views/NotificationsView.js');
    }
}

