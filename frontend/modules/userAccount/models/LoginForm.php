<?php

/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class LoginForm extends CFormModel{
    
    public $identity;
    
    public $password;
    
    public $rememberMe;

    public function rules(){
	return array(
	    array('identity, password', 'required'),
	    array('identity', 'email'),
	    array('rememberMe', 'boolean'),
	    array('password', 'authenticate'),
	);
    }

    public function attributeLabels(){
	return array(
	    'identity' => 'Email',
	    'password' => 'Password',
	    'rememberMe' => 'Remember me'
	);
    }
    
    /**
     * Authenticates the password.
     */
    public function authenticate($attribute, $params)
    {
	    if(!$this->hasErrors())  // we only want to authenticate when no input errors
	    {
		    $identity=new UserIdentity($this->identity, $this->password);
		    $identity->authenticate();
		    switch($identity->errorCode)
		    {
			    case UserIdentity::ERROR_NONE:
				    $duration= $this->rememberMe ? Yii::app()->controller->module->rememberMeTime : 0;
				    Yii::app()->user->login($identity, $duration);
				    break;
			    case UserIdentity::ERROR_UNKNOWN_IDENTITY:
				    $this->addError("identity", "Email is incorrect.");
				    break;
			    case UserIdentity::ERROR_USER_NOT_ACTIVE:
				    $this->addError("status", "You account is not activated.");
				    break;
			    case UserIdentity::ERROR_PASSWORD_INVALID:
				    $this->addError("password", "Password is incorrect.");
				    break;
		    }
	    }
    }
}