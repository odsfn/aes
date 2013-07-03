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
    
    public $recoveryUrl = "/userAccount/recovery/recovery";
    
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

    public function registrate(RegistrationProfile $registrationProfile){
	$transaction = Yii::app()->db->beginTransaction();
	
	try {
	    if($registrationProfile->form->validate()){
	    
		$user = new UserAccount;
		$user->password = $this->encryptPassword($registrationProfile->password);
		$user->status = UserAccount::NEED_ACTIVATION;
		$user->registered = time();
		$user_id = $user->save();

		if(!$user_id){
		    throw new Exception("User registration failed. Cant save User row");
		}

		$userIdentity = new Identity;
		$userIdentity->user_id = $user_id;
		$userIdentity->type = Identity::TYPE_EMAIL;
		$userIdentity->status = Identity::STATUS_NEED_CONFIRMATION;
		$userIdentity->value = $registrationProfile->email;
		$userIdentityId = $userIdentity->save();
		
		if(!$userIdentityId){
		    throw new Exception("User registration failed. Can't save Identity");
		}
	    
		$profile = new Profile;
		$profile->attributes = $registrationProfile->getAttributes($profile->attributeNames());
		$profileId = $profile->save();
		
		if(!$profileId){
		    throw new Exception("User registration failed. Can't save Profile");
		}
		
	    }else{
		throw new Exception("Invalid registration profile");
	    }
	    
	} catch (Exception $exc) {
	    $transaction->rollback();
	    throw $exc;
	}
	
	$transaction->commit();
	$registrationProfile->afterRegistrate($user, $profile, $userIdentity);
    }
    
    protected function encryptPassword($password){
	return md5($password . microtime());
    }
}
