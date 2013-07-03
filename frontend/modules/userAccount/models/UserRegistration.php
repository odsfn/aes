<?php
/*
 * UserRegistration is the Transaction Script that encapsulates registration 
 * proccess business logic which have to be run when user is registrating himself.
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserRegistration extends CComponent{
    /**
     * @var CModel  Registration data model 
     */
    protected $data;

    public function __construct(CModel $data) {
	$this->data = $data;
    }
    
    public function run(){
	$transaction = Yii::app()->db->beginTransaction();
	
	try {
	    if($this->data->validate()){
	    
		$user = new UserAccount;
		$user->setPassword($this->data->password);
		$user->status = UserAccount::STATUS_NEED_ACTIVATION;
		
		if(!$user->save()){
		    throw new Exception("User registration failed. Cant save User row. Errors: " . var_export($user->getErrors(), true));
		}

		$identity = new Identity;
		$identity->user_id = $user->id;
		$identity->type = Identity::TYPE_EMAIL;
		$identity->status = Identity::STATUS_NEED_CONFIRMATION;
		$identity->identity = $this->data->email;
		
		if(!$identity->save())
		    throw new Exception("User registration failed. Can't save Identity. Errors: " . var_export($identity->getErrors(), true));
		
	    
		$profile = new Profile;
		$attributeNames = $profile->attributeNames();
		$attributes = $this->data->getAttributes($attributeNames);
		$profile->setAttributes($attributes, false);
		$profile->user_id = $user->id;
		
		if(!$profile->save()){
		    throw new Exception("User registration failed. Can't save Profile. Errors: " . var_export($profile->getErrors(), true));
		}
		
		$this->afterRecordsCreated($user, $profile, $identity);
	    }else{
		throw new Exception("Invalid registration data. Errors: " . var_export($this->data->getErrors(), true));
	    }
	    
	} catch (Exception $exc) {
	    $transaction->rollback();
	    throw $exc;
	}
	
	$transaction->commit();
	
	return true;
    }
    
    /**
     * Will be called after corresponding records of user account were created, 
     * but before transaction commit
     */
    protected function afterRecordsCreated($user, $profile, $identity){
	$event = new CEvent($this, array(
	    'user'=>$user, 
	    'profile'=>$profile, 
	    'identity'=>$identity)
	);
	$this->raiseEvent('onAfterRecordsCreated', $event);
    }
    
    public function onAfterRecordsCreated($event) {}
}