<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserPageController extends SocialController 
{
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
        
        $usersMandates = array(); 
        $mandates = Mandate::model()->getUsersMandates($this->profile->user_id);
        foreach ($mandates as $key => $mandate) {
            $usersMandates[] = $mandate->id;
        }

        $usersMandates = '[' . implode(',', $usersMandates) . ']';
        
        $this->render('petitions', array(
            'usersMandates' => $usersMandates
        ));
    }    
    
    public function actionPhotos()
    {
        $profileId= $_GET['id'];
        $_GET['id'] = null;
        
        $_GET['target_id'] = $this->profile->target_id;
        
        Yii::app()->getModule('album')->albumRoute = '/userPage/photos/' . $profileId;
        Yii::app()->getModule('album')->imageRoute = '/userPage/photos/' . $profileId . '/action/photo';
        Yii::app()->getModule('album')->ajaxUpdateImageRoute = '/userPage/photos/' . $profileId . '/action/ajaxUpdatePhoto';
        
        $this->beginClip('album');
        
        if(isset($_GET['action']) && $_GET['action'] == 'photo')
            Yii::app()->runController('album/image/photo');
        elseif(isset($_GET['action']) && $_GET['action'] == 'ajaxUpdatePhoto')
            Yii::app()->runController('album/image/ajaxUpdatePhoto');
        else
            Yii::app()->runController('album/image/album');
        
        $this->endClip();
        
        $this->render('photos');
    }
    
    public function actionVideos()
    {   
        $profileId= $_GET['id'];
        Yii::app()->getModule('album')->rootRoute = '/userPage/videos/' . $profileId;
        
        $widgetOut = $this->widget('album.widgets.Gallery', array(
            'type' => 'video',
            'target_id' => $this->profile->target_id,
        ), true);
        
        $this->render('videos', array(
            'galleryWidgetOutput' => $widgetOut
        ));
    }
}
