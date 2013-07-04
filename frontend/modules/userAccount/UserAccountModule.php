<?php
/**
 * @todo 
 * - add Notification component which will encapsulate logic of messages sending
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class UserAccountModule extends CWebModule {
    /**
     * @todo Provide basic layout which can be used in other projects
     * @var string Alias to layout using in module's controllers by default 
     */
    public $layout = 'application.views.layouts.column1';

    public $registrationFormClass = 'RegistrationForm';

    public $registrationUrl = "/userAccount/registration";
    /**
     * This one for user's account activation. 
     * @TODO: 
     * - remove it, use confirmationUrl instead
     * @var string 
     */
    public $activationUrl = "/userAccount/registration/activate";
    
    public $confirmationUrl = "/userAccount/identity/confirm";

    public $recoveryUrl = "/userAccount/recovery";
    
    public $loginUrl = "/userAccount/login";
    
    public $logoutUrl = "/userAccount/login/out";
    
    public $profileUrl = "/userAccount/profile";
    
    public $returnUrl = "/";
    
    public $returnLogoutUrl = "/userAccount/login";    
    
    public $editIdentityUrl = "/userAccount/identity/edit";
    
//    public $afterIdentityEditedUrl = "/userAccount/profile";
    public $afterIdentityEditedUrl = "/userAccount/identity/edit";
    
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
