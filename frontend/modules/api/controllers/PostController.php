<?php
/*
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PostController extends ERestController {
    
    public $nestedModels = array(
        'user' => array('alias' => 'post_author'),
        'comments.user'
    );
    
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
    
    public function doRestCreate($data) {
        $data['user_id'] = Yii::app()->user->id;
        
        $data = $this->removeVirtualAttributes($data);
        
        parent::doRestCreate($data);
    }
    
    public function doRestUpdate($id, $data) {
        
        $data = $this->removeVirtualAttributes($data);
        
        parent::doRestUpdate($id, $data);
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
    
    public function doRestList() {
        $this->outputHelper( 
            'Records Retrieved Successfully', 
            $this->getModel()
                    ->with($this->nestedRelations)
                    ->filter($this->restFilter)
                    ->activeUser()
                    ->postOnly()
                    ->orderBy($this->restSort)
                    ->limit($this->restLimit)->offset($this->restOffset)
            ->findAll(),
            intval($this->getModel()
                    ->filter($this->restFilter)
                    ->activeUser()
                    ->postOnly()
            ->count())
        );
    }
    
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
    
    protected $virtualAttrs = array(
            'displayTime',
            'user',
            'likes',
            'dislikes',
            'comments',
            'createdTs'
    );
}