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
        
        $models = $this->saveModel($this->getModel(), $data);
        
        $this->outputHelper(
            'Record(s) Created',
            $models,
            1
        );
    }
    
    public function doRestList() {
        
        $criteria = $this->getModel()
                    ->with($this->nestedRelations)
                    ->onTarget($this->plainFilter['targetId'])
                    ->postOnly();
        
        $countCriteria = Post::model()
              ->postOnly()
              ->onTarget($this->plainFilter['targetId']);
        
        if(isset($this->plainFilter['usersRecordsOnly']) && $this->plainFilter['usersRecordsOnly']) {
            
            if($this->plainFilter['usersRecordsOnly'] !== 'false') {
                $userPageId = $this->plainFilter['userPageId'];
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

    public function accessRules() {
	return array(
            array('allow',
                'actions' => array('restCreate'),
                'users'   => array('@')
            ),
            array('allow',
                'actions' => array('restDelete', 'restUpdate'),
                'expression' => array($this, 'checkAccess')
            ),
	    array('deny', 
		'actions' => array('restCreate', 'restDelete', 'restUpdate'),
		'users' => array('*')
	    )
	);
    }

    protected function getCheckAccessParams() {
        $id = (int)$_GET['id'];
        
        $model = $this->loadOneModel($id);
        
        $params = array(
            'post' => $model
        );
        
        if($model) {
            $data = $this->data();

            $targetClass = 'Profile';
            $target = new $targetClass;
            $target = $target->findByPk($model->target_id);

            $params[lcfirst($targetClass)] = $target;
        }
        
        return $params;
    }
    
    public function checkAccess() {

        $params = $this->getCheckAccessParams();
        
        if( $this->action->id == 'restUpdate' && Yii::app()->user->checkAccess('updatePost', $params) )
            return true;
        
        if( $this->action->id == 'restDelete' && Yii::app()->user->checkAccess('deletePost', $params) )
            return true;
        
        return false;
    }    
}