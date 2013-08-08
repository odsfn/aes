<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UAccWebUser extends CWebUser {
    /**
     * @var UserAccountModule 
     */
    protected $module;

    public function init() {
	parent::init();
	
	$this->module = Yii::app()->getModule('userAccount');
    }

    public function getModel() {
	$model = $this->module->createUserModel();
	$model = $model->findByPk($this->id);
	return $model;
    }
    
    public function getProfile() {
	return $this->model->profile;
    }
}
