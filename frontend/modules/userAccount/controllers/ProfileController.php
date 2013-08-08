<?php

/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class ProfileController extends UAccController{
    
    public $defaultAction = 'view';
    
    public $profile;
    
    public function actionEdit(){
	$this->profile = $profile = Profile::model()->findByPk(Yii::app()->user->id);
	
	$profile->scenario = 'edit';
	
	if ($this->request->isPostRequest) { 
	    if(!isset($_POST['Profile'])) {
		throw new CHttpException(403);
	    }

	    $profile->attributes = $_POST['Profile'];

	    if ($profile->validate()) {
		$profile->save(false);
		
		//update CWebUser's data
		Yii::app()->user->setState('username', $profile->username);
		
		Yii::app()->user->setFlash('success', Yii::t('common', "Data saved successfully."));
	    }
	}
	
	$this->render('edit', array('model'=>$profile));	
    }
    
    public function actionView(){
	$profile = Profile::model()->findByPk(Yii::app()->user->id);
	
	$this->render('_view', array('data'=>$profile));
    }
    
    public function filters(){
	return array(
	    'accessControl'
	);
    }
    
    public function accessRules() {
	return array(
	    array('allow', 
		'actions' => array('edit', 'view'), 
		'users'=>array('@')
	    ),
	    array('deny', 
		'actions'=>array('edit', 'view'),
		'users'=>array('*')
	    )
	);
    }
}