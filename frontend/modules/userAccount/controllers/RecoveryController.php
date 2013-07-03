<?php

/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */

class RecoveryController extends UAccController {

    public $defaultAction = 'recovery';

    public function actions() {
	return array(
	    'captcha' => array(
		'class' => 'CCaptchaAction',
	    ),
	);
    }

    public function actionRecovery() {
	$form = new RecoveryForm;

	if ($this->request->isPostRequest) {
	    $form->attributes = $_POST['RecoveryForm'];

	    if ($form->validate()) {
		$user = Identity::model()->findByAttributes(array(
			    'identity' => $form->email,
			    'type' => Identity::TYPE_EMAIL
			))->userAccount;

		$this->module->resetPassword($user);

		Yii::app()->user->setFlash('success', 'New password had been sent to your email address.');
		$this->redirect($this->module->loginUrl);
	    }
	}

	$this->render('recovery', array('model'=> $form));
    }

}
