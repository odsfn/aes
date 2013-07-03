<?php

class UserAccountModule extends CWebModule {
    /**
     * Put your registration profile class here to implement custom registration
     * logic. This class should extend RegistrationProfile
     * 
     * @var string class name
     */
    public $registrationProfileClass = 'RegistrationProfile';

    public $registrationFormClass = 'RegistrationForm';

    public $registrationUrl = "/userAccount/registration";
    
    public $activationUrl = "/userAccount/registration/activate";
    
    public $recoveryUrl = "/userAccount/recovery";
    
    public $loginUrl = "/userAccount/login";
    
    public $logoutUrl = "/userAccount/login/out";
    
    public $profileUrl = "/userAccount/profile";
    
    public $returnUrl = "/";
    
    public $returnLogoutUrl = "/userAccount/login";    
    
    public $rememberMeTime = 3600;
    
    public function init() {
	// this method is called when the module is being created
	// you may place code here to customize the module or the application
	// import the module-level models and components
	$this->setImport(array(
	    'userAccount.models.*',
	    'userAccount.components.*',
	));
    }

    public function beforeControllerAction($controller, $action) {
	if (parent::beforeControllerAction($controller, $action)) {
	    // this method is called before any module controller action is performed
	    // you may place customized code here
	    return true;
	}
	else
	    return false;
    }
    
    /**
     * Resets user's password and send it to email
     * @param UserAccount $user
     */
    public function resetPassword(UserAccount $user){
	if($user->status != UserAccount::STATUS_ACTIVE)
	    throw new CException('Can\'t reset password for inactive users.');
	
	$emailAddr = $user->getActiveEmail();
	
	$newPassword = $this->randomPassword();
	$user->setPassword($newPassword);
	$user->save();
	
	$email = new YiiMailer('resetPassword', $data = array(
	    'newPassword' => $newPassword,
	    'description' => $description = 'Password reset'
	));
	
	$email->setSubject($description);
	$email->setTo($emailAddr);
	$email->setFrom(Yii::app()->params['noreplyAddress'], Yii::app()->name, FALSE);
	
	Yii::log('Sendign reset password mail to ' . $emailAddr);
	
	if($email->send())
	    Yii::log('Ok');
	else{
	    Yii::log('Failed');
	    throw new CException('Failed to send the email');
	}
    }
    
    function randomPassword() {
	$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 8; $i++) {
	    $n = rand(0, $alphaLength);
	    $pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
    }
}
