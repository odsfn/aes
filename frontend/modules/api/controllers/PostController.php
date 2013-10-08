<?php
/*
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PostController extends RestController {
    
    public $acceptableFilters = array(
        'plain' => 'userPageId',
        'model' => 'conversation_id'
    );    
    
    public $nestedModels = array(
        
        'user' => array(
            'alias' => 'post_author',
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
        ),
        
        'comments.user' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
        ),
        
        'rates' => array('alias' => 'post_rate'),
        
        'comments.rates'
    );
        
    public $virtualAttrs = array(
            'displayTime',
            'user',
            'likes',
            'dislikes',
            'comments',
            'createdTs',
            'targetId'
    );
    
    public function doRestCreate($data) {
        $data['user_id'] = Yii::app()->user->id;
        
        $targetId = $data['targetId'];
        
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
    
    public function doRestList() {
        
        $criteria = $this->getModel()
                    ->with($this->nestedRelations)
                    ->onUsersPage($userPageId = $this->plainFilter['userPageId'])
                    ->postOnly();
        
        $countCriteria = PostPlacement::model()
            ->postsOnUsersPage($userPageId);
        
        if(isset($this->plainFilter['usersRecordsOnly']) && $this->plainFilter['usersRecordsOnly']) {
            
            if($this->plainFilter['usersRecordsOnly'] !== 'false') {
                $criteria->usersOnly($userPageId);
                $countCriteria->usersOnly($userPageId);
            }
            
        }
        
        $criteria->orderBy('created_ts', 'DESC');
        
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
    
}