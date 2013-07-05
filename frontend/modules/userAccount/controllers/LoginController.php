<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class LoginController extends UAccController{
    
    public $defaultAction = 'login';
    
    public function actionLogin(){
	$form = new LoginForm;
	if($this->request->isPostRequest){
	    $form->attributes = $_POST['LoginForm'];
	    if($form->validate()){
		$this->redirect($this->module->returnUrl);
	    }
	}
	
	$this->render('login', array('model'=>$form));
    }
    
    public function actionOut(){
	Yii::app()->user->logout();
	$this->redirect(Yii::app()->controller->module->returnLogoutUrl);
    }

    public function filters(){
	return array(
	    'accessControl'
	);
    }
    
    public function accessRules() {
	return array(
	    array('allow', 
		'actions' => array('out'), 
		'users'=>array('@')
	    ),
	    array('allow', 
		'actions'=>array('login'),
		'users'=>array('?')
	    ),
	    array('deny', 
		'actions'=>array('out', 'login'),
		'users'=>array('*')
	    )
	);
    }
}
