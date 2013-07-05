<?php
/*
 * Controls registration proccess
 * @TODO: 
 *  - Separate RegistrationForm for Profile, User, Identity parts
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class RegistrationController extends UAccController {

    public $defaultAction = 'registration';

    public function actionRegistration() {
	
	if (Yii::app()->user->id) {
	    $this->redirect($this->module->profileUrl);
	    return;
	}
	
	$form = $this->createRegistrationForm();
	$this->performAjaxValidation($form, 'RegistrationForm');
	
	if ($this->request->isPostRequest) { 
	    if(!isset($_POST['RegistrationForm'])) {
		throw new CHttpException(403);
	    }
	
	    $form->attributes = $_POST['RegistrationForm'];
	
	    if ($form->validate()) {
		$registration = new UserRegistration($form);
		$registration->onAfterRecordsCreated = array($this, 'afterRecordsCreated');
		try {
		    $registration->run();
		    Yii::app()->user->setFlash('success', "We have created account especially for you! Please check your mail, and confirm registration");
		    $this->redirect($this->module->loginUrl);
		} catch (Exception $exc) {
		    throw $exc;
//		    Yii::app()->user->setFlash('error', "Sorry, but something went wrong during registration process. Try later or contact with administrator");
		}
	    }
	}

	$this->render('registration', array('model' => $form));
    }

    public function actionActivate($key){
	if(!$key){
	    throw new CHttpException(404, "Activation key should be specified");
	}
	
	$confirmation = IdentityConfirmation::model()->findByAttributes(array('key'=>$key));
	if(!$confirmation){
	    throw new CHttpException(404, "Specified confirmation was not found");
	}
	
	$confirmation->confirm();
	Yii::app()->user->setFlash('success', "Your account activated successfully. You can login now.");
	$this->redirect(array($this->module->loginUrl));
    }
    
    protected function createRegistrationForm(){
	$registrationFormClass = $this->module->registrationFormClass; 
	return new $registrationFormClass('registration');
    }
    /**
     * Runs after user's records were created in the database. Contains logic
     * for sending activation mail. 
     * 
     * @param СEvent $event
     * @throws CException
     */
    protected function afterRecordsCreated($event){
	$identity = $event->params['identity'];
	$confirmation = $identity->startConfirmation();
	$activationUrl = $this->createAbsoluteUrl($this->module->activationUrl, array('key'=>$confirmation->key));
	
	$email = new YiiMailer('activation', $data = array(
	    'activationUrl' => $activationUrl,
	    'description' => $description = 'Account activation'
	));
	$email->setSubject($description);
	$email->setTo($identity->identity);
	$email->setFrom(Yii::app()->params['noreplyAddress'], Yii::app()->name, FALSE);
	
	Yii::log('Sendign activation mail to ' . $identity->identity . ' with data: ' . var_export($data, true));
	
	if($email->send())
	    Yii::log('Ok');
	else{
	    Yii::log('Failed');
	    throw new CException('Failed to send the email');
	}
    }
    
    public function filters(){
	return array(
	    'accessControl'
	);
    }
    
    public function accessRules() {
	return array(
	    array('allow', 
		'actions' => array('registration', 'activate'), 
		'users'=>array('?')
	    ),
	    array('deny', 
		'actions'=>array('registration', 'activate'),
		'users'=>array('*')
	    )
	);
    }
}