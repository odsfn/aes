<?php

/*
 * Base for all REST controllers
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class RestController extends ERestController {
    
    /**
     * Attributes which exists only on client side. Thay can't be saved as models'
     * properties, so they should be removed, before model saving start.
     * @var array 
     */
    public $virtualAttrs = array();
    
    /**
     * Model's property name which contains value of the owner. Used during default
     * access filters run.
     * @var string 
     */
    public $modelsUserIdAttr = 'user_id';
    
    /**
     * Override to ignore any access restrictions. 
     * @TODO: Implement it for security needs
     */
    public function filterRestAccessRules($c) {
        Yii::app()->clientScript->reset(); //Remove any scripts registered by Controller Class
        $c->run();
    }
    
    /**
     * This output helper has been overriden for compability with Backbone.parse method
     */
    public function outputHelper($message, $results, $totalCount = 0, $model = null) {
        parent::outputHelper($message, $results, $totalCount, $model = 'models');
    }
    
    /**
     * Cleans array of models' attributes from attributes that are exist only on client side ( not in the DB )
     *  
     * @param array $data   Models data gotten from REST request
     * @param array $virtualAttrs   Attributes that should be removed
     * @return array
     */
    protected function removeVirtualAttributes($data, $virtualAttrs = null) {
         
        if(!$virtualAttrs) 
             $virtualAttrs = $this->virtualAttrs;
         
        foreach ($data as $key => $value) {
             
             if(empty($value)) {
                 unset($data[$key]);
                 continue;
             }
             
             if(in_array($key, $virtualAttrs)) {
                 if(is_array($virtualAttrs[$key]) && is_array($data[$key])) {
                     $data[$key] = $this->removeVirtualAttributes($data[$key], $virtualAttrs[$key]);
                 }else{
                     unset($data[$key]);
                 }
             }
         }
         
         return $data;
    }
    
    protected function saveModel($model, $data) {
        $data = $this->removeVirtualAttributes($data);
        
        return parent::saveModel($model, $data);
    }
    
    public function doRestDelete($id) {
        $model = $this->loadOneModel($id);
        if (is_null($model)) {
            $this->HTTPStatus = $this->getHttpStatus(404);
            throw new CHttpException(404, 'Record Not Found');
        } else {
            if ($model->delete())
                $this->outputHelper('Record(s) Deleted', array($model), 1);
            else {
                $this->HTTPStatus = $this->getHttpStatus(406);
                throw new CHttpException(406, 'Could not delete model with ID: ' . $id);
            }
        }
    }
    
    public function _filters(){
	return array(
	    'accessControl'
	);
    }
    
    public function accessRules() {
	return array(
	    array('allow', 
		'actions' => array('restCreate'), 
		'users'=>array('@')
	    ),
            array('allow',
                'actions' => array('restDelete', 'restUpdate'),
                'expression' => array($this, 'doesUserCanControlModel')
            ),
	    array('deny', 
		'actions'=>array('restCreate', 'restDelete', 'restUpdate'),
		'users'=>array('*')
	    )
	);
    }
    
    public function doesUserCanControlModel($user, $rule) {
        $id = $_GET['id'];
        
        $model = $this->loadOneModel($id);
        
        if($this->isUserOwnsModel($model, $user))
            return true;
        
        return false;
    }
    
    /**
     * Checks whather the user has access to the model.
     * 
     * @param CActiveRecord $model
     * @param CWebUser $user
     * @return boolean
     */
    protected function isUserOwnsModel($model, $user = null) {
        if(!$user)
            $user = Yii::app()->user; 
        
        return $model->{$this->modelsUserIdAttr} == $user->id;
    }
}
