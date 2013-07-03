<?php

/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class RecoveryForm extends CFormModel{

    public $email;
    
    public $verifyCode;
    
    public function rules(){
	return array(
	    array('email, verifyCode', 'required'),
	    array('email', 'email'),
	    array('email', 'exist', 'attributeName'=>'identity', 'className'=>'Identity', 'message'=>'We have no users with this email address'),
	    array('verifyCode', 'captcha')
	);
    }
    
    public function attributeLabels() {
	return array(
	    'email',
	    'verifyCode' => 'Verification code'
	);
    }
}