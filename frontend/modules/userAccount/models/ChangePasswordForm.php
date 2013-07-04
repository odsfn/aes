<?php

/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class ChangePasswordForm extends CFormModel {

    public $password;
    public $password_check;

    public function rules() {
	$rules = parent::rules();
	return array_merge($rules, array(
	    array('password, password_check', 'length', 'max' => 128, 'min' => 6,
		'message' => 'Incorrect password (minimal length 6 symbols).'
	    ),
	    array('password_check', 'compare', 'compareAttribute' => 'password',
		'message' => "Retype Password is incorrect."
	    ),
	));
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
	return array(
	    'password' => 'New password',
	    'password_check' => 'Confirm new password'
	);
    }

}
