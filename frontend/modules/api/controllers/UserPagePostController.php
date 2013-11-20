<?php
Yii::import('frontend.modules.api.controllers.PostController');
/**
 * Custom post controller for user page. It differs from common by filters and 
 * access rules
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class UserPagePostController extends PostController {
    
    public function getAcceptableFilters() {
        return array_merge(parent::getAcceptableFilters(), array('plain' => 'userPageId'));
    }  
    
    public function getModel() 
    {
        $this->model = new Post;
        
        return parent::getModel();
    }    
    
    protected function applyCustomFilters($criteria, $countCriteria) {
        if(isset($this->plainFilter['usersRecordsOnly']) && $this->plainFilter['usersRecordsOnly']) {
            
            if($this->plainFilter['usersRecordsOnly'] !== 'false') {
                $userPageId = $this->plainFilter['userPageId'];
                $criteria->usersOnly($userPageId);
                $countCriteria->usersOnly($userPageId);
            }
            
        }
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
            $target = $model->target->row;
            $targetClass = get_class($target);
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