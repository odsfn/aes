<?php
/*
 * Extends Profile by additional fields for registration proccess needs. Incapsulates
 * registration proccess
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class RegistrationForm extends Profile{

    public $password;
    
    public $password_check;
    
    public function rules() {
	$rules = parent::rules();
	return array_merge($rules, array(
	    
	    array('password, password_check', 'required'),
	    
	    array('password, password_check', 'length', 'max'=>128, 'min' => 6, 
		'message' => 'Incorrect password (minimal length 6 symbols).'
	    ),
	    
	    array('password_check', 'compare', 'compareAttribute'=>'password', 
		'message' => "Retype Password is incorrect."
	    )
	));
    }
}