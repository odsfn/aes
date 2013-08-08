<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserPageController extends EController {

    public $profile;
    /**
     * Authenticated user's page
     */
    public function actionIndex() {
	
	$user = Yii::app()->user;
	
	if($user->isGuest) 
	    $this->redirect ('/');
	
	$this->profile = $user->profile;
	
	$this->render('userPage', array('profile' => $this->profile));
    }
}
