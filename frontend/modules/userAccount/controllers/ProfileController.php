<?php

/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class ProfileController extends UAccController{
    
    public $defaultAction = 'view';
    
    public function actionEdit(){
	
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