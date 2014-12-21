<?php
/**
 * Rest controller for almost all types of comments ( except comments to the posts
 * they are processed by PostController )
 * 
 * If you want to provide new commentable entity in the system you should follow such steps:
 * 1. Create tables for comments using CommentableDbManagerHelper in the migration script
 * 2. Create models for new tables extending them from Comment model.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class CommentController extends RestController {
    
    protected $targetType;

    public $acceptableFilters = array(
        'plain' => 'target_id,target_type'
    );    
    
    public $nestedModels = array(
        
        'user' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
        ),
        
        'rates' => array('alias' => 'post_rate')
        
    );
        
    public $virtualAttrs = array(
            'user',
            'likes',
            'dislikes'
    );

    public function getOutputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'toTs'),
            'last_update_ts' => array('Formatter', 'toTs'),
            'rates.created_ts' => array('Formatter', 'toTs')
        );
    }

    public function getInputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'fromTs'),
            'last_update_ts' => array('Formatter', 'fromTs')
        );
    }
    
    public function getModel() 
    {
        if(!$this->model) {
            $model = $_GET['target_type'] . 'Comment';
            
            $this->targetType = $_GET['target_type'];
            unset($_GET['target_type']);

            try {
                $this->model = new $model;
            } catch (Exception $e) {
                throw new Exception('You should provide existing model name in "target_type" request attribute', null, $e);
            }
        }
        
        return parent::getModel();
    }
    
    public function doRestList() {
        
        $criteria = $this->getModel();
                
        $criteria->with($this->nestedRelations)
                ->criteriaToTarget($this->plainFilter['target_id']);
        
        $countCriteria = clone $criteria;
        
        $totalCount = $countCriteria->count();
        
        $this->outputHelper( 
            'Records Retrieved Successfully',
            $criteria
                ->limit($this->restLimit)
                ->offset($this->restOffset)
                ->findAll(array('order' => 'created_ts DESC')),
            (int)$totalCount
        );
    }
    
    public function doRestCreate($data) {
        $data['user_id'] = Yii::app()->user->id;
        
        parent::doRestCreate($data);
    }
    
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

    public function checkAccess() {
        $id = (int)$_GET['id'];
        
        $model = $this->loadOneModel($id);
        
        $params = array(
            'comment' => $model
        );
        
        if($model) 
            $target = $model->target;
        else {  //model was not initialized because check is performing during createComment
            $data = $this->data();
            
            if(!$data) 
                $data = $_GET['filter'];
            
            $targetClass = $this->targetType;
            $target = new $targetClass;
            $target = $target->findByPk($data['target_id']);
        }
        
        if(! $target instanceof iCommentable ) {
            $canUnassignedComment = true;
            $canUnassignedRead = true;
        } else {
            $canUnassignedComment = $target->canUnassignedComment();
            $canUnassignedRead = $target->canUnassignedRead();
        }
        
        $params[lcfirst($this->targetType)] = $target;
        $params['target'] = $target;
        
        $disabledRoles = array();
        
        if(!$canUnassignedComment)
            $disabledRoles[] = 'commentor';
        
        if(!$canUnassignedRead)
            $disabledRoles[] = 'commentReader';
        
        if(!method_exists($target, 'checkUserInRole')) {
            $disabledRoles[] = 'commentModerator';
        }
        
        $params['disabledRoles'] = $disabledRoles;
        
        if( ( $this->action->id == 'restList' || $this->action->id == 'restView' ) && Yii::app()->user->checkAccess('readComment', $params) )
            return true;
        
        if( $this->action->id == 'restCreate' && Yii::app()->user->checkAccess('createComment', $params) )
            return true;
        
        if( $this->action->id == 'restUpdate' && Yii::app()->user->checkAccess('updateComment', $params) )
            return true;
        
        if( $this->action->id == 'restDelete' && Yii::app()->user->checkAccess('deleteComment', $params) )
            return true;
        
        return false;
    }    
}