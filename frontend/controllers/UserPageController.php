<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserPageController extends SocialController {

    public $profile;
    
    public $self = false;
    
    /**
     * Authenticated user's page
     */
    public function actionIndex() {
        
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
	
	$this->render('userPage', array('profile' => $this->profile, 'self' => $this->self));
    }
}
