<?php
/*
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PostController extends ERestController {
    
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
}