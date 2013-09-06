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
        
        $targetId = $data['targetId'];
        
        $data = $this->removeVirtualAttributes($data);
        
        $models = $this->saveModel($this->getModel(), $data);
        
        $post = $models[0];
        
        if(!$post->reply_to) {
            $placement = new PostPlacement();
            $placement->post_id = $post->id;
            $placement->placer_id = $post->user_id;
            $placement->placed_ts = $post->created_ts;
            $placement->target_id = $targetId;
            $placement->target_type = PostPlacement::TYPE_USER_PAGE;
            $placement->save();
        }
        
        $this->outputHelper(
            'Record(s) Created',
            $models,
            1
        );
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
        
        $criteria = $this->getModel()
                    ->with($this->nestedRelations)
                    ->onUsersPage($userPageId = $this->restFilter['userPageId'])
                    ->postOnly();
        
        $countCriteria = PostPlacement::model()
            ->postsOnUsersPage($userPageId);

        
        unset($this->restFilter['userPageId']);
        
        if(isset($this->restFilter['usersRecordsOnly']) && $this->restFilter['usersRecordsOnly']) {
            
            if($this->restFilter['usersRecordsOnly'] !== 'false') {
                $criteria->usersOnly($userPageId);
                $countCriteria->usersOnly($userPageId);
            }
            
            unset($this->restFilter['usersRecordsOnly']);
        }
        
        $criteria->filter($this->restFilter);
        
        $totalCount = $countCriteria->count();
        
        $this->outputHelper( 
            'Records Retrieved Successfully',
            $criteria
                ->limit($this->restLimit)
                ->offset($this->restOffset)
                ->findAll(),
            (int)$totalCount
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
            'createdTs',
            'targetId'
    );
}