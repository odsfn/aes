<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserIdentity extends CUserIdentity{
    
    const ERROR_USER_NOT_ACTIVE = 3;
    
    public function authenticate() {
	$identityRow = Identity::model()->findByAttributes(array(
	    'identity'=> $this->username,
	    'type'=> Identity::TYPE_EMAIL,
	    'status'=> Identity::STATUS_CONFIRMED
	));
	
	if($identityRow){
	    $user = $identityRow->userAccount;
	} 
	
	if(!$identityRow || !$user){
	    $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;    
	}
	
	else if(!$user->passwordEquals($this->password)){
	    $this->errorCode = self::ERROR_PASSWORD_INVALID;
	}
	
	else if($user->status != UserAccount::STATUS_ACTIVE){
	    $this->errorCode = self::ERROR_USER_NOT_ACTIVE; 
	}
	
	else{
	    $this->errorCode = self::ERROR_NONE;
	    $this->setState('id', $user->id);
	    $this->setState('username', $user->profile->username);
	}
	
	return !$this->errorCode;
    }
    
    public function getId() {
	return $this->getState('id');
    }
    
    public function getName() {
	return $this->getState('username');
    }
}
