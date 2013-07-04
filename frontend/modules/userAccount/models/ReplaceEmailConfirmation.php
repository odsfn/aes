<?php

/*
 * Have to be confirmed when user changes his email address
 * @author Vasiliy Pedak truvazia@gmail.com
 */

class ReplaceEmailConfirmation extends IdentityConfirmation{
    
    public $successMessage = "New email confirmed successfully";
    
    public $errorMessage = 'Failure';
    
    protected function beforeConfirm(){
	//delete old email identity here
	$this->dbConnection
	    ->createCommand('DELETE FROM user_identity WHERE type = "' . Identity::TYPE_EMAIL 
		    . '" AND id != "' . $this->identity->id . '"'
		    . ' AND user_id = "' . $this->identity->user_id . '"')
	    ->execute();
	
	//updating profile
	$confirmatingEmail = $this->identity->identity;
	$profile = Profile::model()->findByPk($this->identity->user_id);
	$profile->email = $confirmatingEmail;
	$profile->save();
	
	return true;
    }
    
    protected function afterConfirm() {
    }
}
