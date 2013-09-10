<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PostRateController extends RestController {
    
    public $nestedModels = array();
    
    public $virtualAttrs = array('createdTs');
    
    public function doRestCreate($data) {
        $data['user_id'] = Yii::app()->user->id;
        parent::doRestCreate($data);
    }
}