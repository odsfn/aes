<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserPageController extends SocialController {

    /**
     * Authenticated user's page
     */
    public function actionIndex() {
        $this->render('userPage');
    }
    
    public function actionNominations() {
        
        $canControl = ($this->profile->user_id == Yii::app()->user->id);
        
        $this->render('nominations', array('canControl' => (int)$canControl, 'userId' => $this->profile->user_id));
    }    
}
