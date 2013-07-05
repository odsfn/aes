<?php

/*
 * Controls settings of user's identity data ( email )
 * @TODO: 
 * - Add phone support
 * - Add external services support
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class IdentityController extends UAccController{
    
    public $defaultAction = 'checkAccess';
    
    public function actionCheckAccess(){
	//Will show the form with password field to prevent 
	//identity data to be changed from other persone
    }
    
    /**
     * Provides ability to change password and email address.
     * If user want to change email it will be changed after confirmation of
     * new email address.
     * 
     * @throws CException
     */
    public function actionEdit(){
	$identity = Identity::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));
	
	$newPassword = new ChangePasswordForm;
	
	if($this->request->isPostRequest){
	    
	    if($identity->identity !== $_POST['Identity']['identity']){
		$newEmail = $_POST['Identity']['identity'];
		$storedIdentity = clone $identity;
		$identity->identity = $newEmail;
	    }
	
	    $newPassword->attributes = $_POST['ChangePasswordForm'];
	    
	    $isFormValid = $newPassword->validate();
	    
	    if($isFormValid && $newEmail){
		$isFormValid =  $identity->validate();
	    }
	    
	    if($isFormValid && isset($newEmail)){
		$identity->status = Identity::STATUS_NEED_CONFIRMATION;
		$identity->isNewRecord = true;
		$identity->id = null;
		$identity->save();
		
		$confirmation = $identity->startConfirmation(IdentityConfirmation::TYPE_EMAIL_REPLACE_CONFIRMATION);

		$activationUrl = $this->createAbsoluteUrl($this->module->confirmationUrl, array('key'=>$confirmation->key));
	
		$email = new YiiMailer('changeEmail', $data = array(
		    'activationUrl' => $activationUrl,
		    'description' => $description = 'Email change confirmation'
		));
		$email->setSubject($description);
		$email->setTo($identity->identity);
		$email->setFrom(Yii::app()->params['noreplyAddress'], Yii::app()->name, FALSE);

		Yii::log('Sendign email change confirmation to ' . $identity->identity . ' with data: ' . var_export($data, true));
		
		// @TODO: catch mailing exceptions here, to give user right messages
		if($email->send())
		    Yii::log('Ok');
		else{
		    Yii::log('Failed');
		    throw new CException('Failed to send the email');
		}
		
		Yii::app()->user->setFlash('info', 'Your new email will be applied after confirmation. Please, check this email address ' . $newEmail . '. You should get confirmation mail there.');
	    }
	    
	    if($isFormValid){
		$user = $identity->userAccount;
		
		if($newPassword->password && !$user->passwordEquals($newPassword->password)){
		    $user->setPassword($newPassword->password);
		    $user->save();
		    
		    Yii::app()->user->setFlash('success', 'Password has been changed successfully');
		}
	    }
	    
	    if($isFormValid){
		$this->redirect($this->module->afterIdentityEditedUrl);
	    }
	}
	
	$this->render('edit', array('identity'=>$identity, 'newPassword'=>$newPassword));
    }
    
    /**
     * Action for run different confirmations. Such as email changing...
     * @param string $key
     * @throws CHttpException
     */
    public function actionConfirm($key){
	if(!$key){
	    throw new CHttpException(404, "Confirmation key should be specified");
	}
	
	$confirmation = IdentityConfirmation::create($key);
	
	if(!$confirmation){
	    throw new CHttpException(404, "Specified confirmation was not found");
	}
	
	if($confirmation->confirm()){
	    Yii::app()->user->setFlash('success', $confirmation->successMessage);
	}else{
	    Yii::app()->user->setFlash('error', $confirmation->errorMessage);
	}
	
	$this->redirect(Yii::app()->user->isGuest ? array($this->module->loginUrl) : array($this->module->profileUrl) );
    }
    
    public function filters(){
	return array(
	    'accessControl'
	);
    }
    
    public function accessRules() {
	return array(
	    array('allow', 
		'actions' => array('edit'), 
		'users'=>array('@')
	    ),
	    array('deny', 
		'actions'=>array('edit'),
		'users'=>array('*')
	    )
	);
    }
}