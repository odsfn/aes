<?php
/*
 * Basic controller for module
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UAccController extends CController{
    /**
     * Common ajax validation logic 
     * 
     * @param string $model
     * @param CModel $form
     */
    protected function performAjaxValidation($model, $form) {
	if(isset($_POST['ajax']) && $_POST['ajax'] == $form) {
	    echo CActiveForm::validate($model);
	    Yii::app()->end();
	}
    }
    
    protected function getRequest(){
	return Yii::app()->request;
    }
}