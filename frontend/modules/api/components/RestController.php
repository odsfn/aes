<?php

/*
 * Base for all REST controllers
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class RestController extends ERestController {
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

    protected $virtualAttrs = array();
}
