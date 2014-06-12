<?php
/*
 * Customized registration controller
 * 
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
Yii::import('userAccount.controllers.RegistrationController');
Yii::import('personIdentifier.models.PersonIdentifier');

class AesRegistrationController extends RegistrationController
{
    protected $personIdent;

    public function actionRegistration()
    {

        if (Yii::app()->user->id) {
            $this->redirect(array($this->module->profileUrl));
            return;
        }

        $regForm = $this->createRegistrationForm();
        
        $personIdent = new PersonIdentifier('userApply');
        
        if(isset($_POST['PersonIdentifier']['type']))
            $personIdent->type = $_POST['PersonIdentifier']['type'];
        else
            $personIdent->type = Yii::app()->getModule('personIdentifier')->defaultIdentifierType;
        
//        $this->performAjaxValidation($regForm, 'RegistrationForm');
//        $this->performAjaxValidation($personIdent, 'PersonIdentifier');
        
        if ($this->request->isPostRequest) {
            if (!isset($_POST['RegistrationForm'])) {
                throw new CHttpException(403);
            }

            $regForm->attributes = $_POST['RegistrationForm'];
            $personIdent->attributes = $_POST['PersonIdentifier'];
            
            $valid = $regForm->validate();
            $valid = $personIdent->validate() && $valid;
            
            $this->personIdent = $personIdent;
            
            if ($valid) {
                $registration = new UserRegistration($regForm);
                $registration->onAfterRecordsCreated = array($this, 'afterRecordsCreated');
                try {
                    $registration->run();
                    Yii::app()->user->setFlash('success', "We have created account especially for you! Please check your mail, and confirm registration");
                    $this->redirect(array($this->module->loginUrl));
                } catch (Exception $exc) {
                    throw $exc;
//		    Yii::app()->user->setFlash('error', "Sorry, but something went wrong during registration process. Try later or contact with administrator");
                }
            }
        }

        $this->render($this->module->registrationView, array(
            'model' => $regForm, 
            'personIdent' => $personIdent
        ));
    }

    protected function afterRecordsCreated($event)
    {
        $this->personIdent->setScenario('insert');
        $this->personIdent->profile_id = $event->params['profile']->user_id;
        $this->personIdent->save();
        
        parent::afterRecordsCreated($event);
    }
}
