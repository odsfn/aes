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
    
    public function actionVotes() {
        $this->render('votes', array('canControl' => (int)$canControl, 'userId' => $this->profile->user_id));
    }
    
    public function actionMandates() {
        $this->render('mandates');
    }
    
    public function actionPetitions() {
        $usersMandates = 'false';
        
        if ($uid = Yii::app()->user->id) {
            $usersMandates = array(); 
            $mandates = Mandate::model()->getUsersMandates($uid);
            foreach ($mandates as $key => $mandate) {
                $usersMandates[] = $mandate->id;
            }
            
            $usersMandates = '[' . implode(',', $usersMandates) . ']';
        }
        
        $this->render('petitions', array(
            'usersMandates' => $usersMandates
        ));
    }    
}
