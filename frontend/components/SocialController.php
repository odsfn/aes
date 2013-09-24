<?php
/*
 * Base controller for user's pages.
 */
class SocialController extends FrontController {
    
    public $profile;
    
    public $self = false;
    
    public $layout = '//layouts/user';
    
    public function init() {
        $this->attachBehavior('breadcrumbs', new CrumbsBehaviour);
        $this->breadcrumbs->setEnabled(true);
        
        $this->initProfile();
        
        parent::init();
    }
    
    protected function initProfile() {
        if(isset($_GET['id'])) {
            
            $userId = (int)$_GET['id'];
            $user = UserAccount::model()->findByPk($userId);
            
            if(!$user) {
                throw new CHttpException(404);
            }
            
            if(!Yii::app()->user->isGuest && $userId == Yii::app()->user->id)
                $this->self = true;
            
        } else {
            $user = Yii::app()->user;
            
            if($user->isGuest) 
                $this->redirect('/');
            
            $this->self = true;
        }
        
	$this->profile = $user->profile;        
    }
    
    public function render($view, $data = null, $return = false) {
        
        $data = array_merge(array(
            'profile' => $this->profile, 'self' => $this->self
        ));
        
        parent::render($view, $data, $return);
    }
}
