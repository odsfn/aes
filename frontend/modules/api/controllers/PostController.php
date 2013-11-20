<?php
/*
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PostController extends RestController {
    
    public function getAcceptableFilters() {
        return array();
    }
    
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
            'createdTs'
    );
    

    public function getOutputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'toTs'),
            'last_update_ts' => array('Formatter', 'toTs'),
            'rates.created_ts' => array('Formatter', 'toTs'),
            'comments.created_ts' => array('Formatter', 'toTs'),
            'comments.last_update_ts' => array('Formatter', 'toTs')
        );
    }

    public function getInputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'fromTs'),
            'last_update_ts' => array('Formatter', 'fromTs')
        );
    }    
    
    public function doRestCreate($data) {
        $data['user_id'] = Yii::app()->user->id;
        
        $models = $this->saveModel($this->getModel(), $data);
        
        $this->outputHelper(
            'Record(s) Created',
            $models,
            1
        );
    }   
    
    protected function applyCustomFilters($criteria, $countCriteria) {}

    public function doRestList() {
        
        $criteria = $this->getModel()
                    ->with($this->nestedRelations)
                    ->onTarget($this->plainFilter['target_id'])
                    ->postOnly();
        
        $countCriteria = Post::model()
              ->postOnly()
              ->onTarget($this->plainFilter['target_id']);
        
        $this->applyCustomFilters($criteria, $countCriteria);
        
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

    protected function getCheckAccessParams() {
        $id = (int)$_GET['id'];
        
        $model = $this->loadOneModel($id);
        
        $params = array(
            'post' => $model
        );
        
        if($model) {
            $data = $this->data();

            $target = $model->target->row;
            
            $params[lcfirst($targetClass)] = $target;
        }
        
        return $params;
    }
    
//    public function checkAccess() {
//
//        $params = $this->getCheckAccessParams();
//        
//        if( $this->action->id == 'restUpdate' && Yii::app()->user->checkAccess('updatePost', $params) )
//            return true;
//        
//        if( $this->action->id == 'restDelete' && Yii::app()->user->checkAccess('deletePost', $params) )
//            return true;
//        
//        return false;
//    }
    
    public function accessRules() {
	return array(
            array('allow',
                'actions' => array('restList', 'restView', 'restCreate', 'restDelete', 'restUpdate'),
                'expression' => array($this, 'checkAccess')
            ),
	    array('deny', 
		'actions' => array('restList', 'restView', 'restCreate', 'restDelete', 'restUpdate'),
		'users' => array('*')
	    )
	);
    }

    protected function getTargetType() {
        return $_GET['target_type'];
    }
    
    public function checkAccess() {
        $id = (int)$_GET['id'];
        
        $model = $this->loadOneModel($id);
        
        $params = array(
            'post' => $model
        );
        
        if($model) 
            $target = $model->target->row;
        else {  //model was not initialized because check is performing during createComment
            $data = $this->data();
            
            if(!$data) 
                $data = $_GET['filter'];
            
            $targetClass = $this->targetType;
            $target = new $targetClass;
            $target = $target->findByAttributes(array('target_id'=>$data['target_id']));
        }
        
        if(! $target instanceof iPostable )
            throw new Exception ('Post target ( post is being added to this object ) should be an instance of iPostable');
        
        $params[lcfirst($this->targetType)] = $target;
        
        $disabledRoles = array();
        
        if(!$target->canUnassignedPost())
            $disabledRoles[] = 'poster';
        
        if(!$target->canUnassignedReadPost())
            $disabledRoles[] = 'postReader';
        
        $params['disabledRoles'] = $disabledRoles;
        
        if( ( $this->action->id == 'restList' || $this->action->id == 'restView' ) && Yii::app()->user->checkAccess('readPost', $params) )
            return true;
        
        if( $this->action->id == 'restCreate' && Yii::app()->user->checkAccess('createPost', $params) )
            return true;
        
        if( $this->action->id == 'restUpdate' && Yii::app()->user->checkAccess('updatePost', $params) )
            return true;
        
        if( $this->action->id == 'restDelete' && Yii::app()->user->checkAccess('deletePost', $params) )
            return true;
        
        return false;
    }    
}