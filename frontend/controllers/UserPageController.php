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
        
        $canControl = $this->canControlComments();
        
        $this->render('nominations', array('canControl' => (int)$canControl, 'userId' => $this->profile->user_id));
    }
    
    public function actionVotes() {
        $this->canControlComments();
        
        $this->render('votes', array('userId' => $this->profile->user_id));
    }
    
    public function actionMandates() {
        $this->canControlComments();
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
        $this->canControlComments();
        $profileId= $_GET['id'];
        
        Yii::app()->getModule('album')->rootRoute = '/userPage/photos/' . $profileId;
        
        $this->beginClip('album');
        echo $this->widget('album.widgets.Gallery', array(
            'type' => 'image',
            'target_id' => $this->profile->target_id,
        ), true);
        $this->endClip();
        
        $this->render('photos');
    }
    
    public function actionVideos()
    {   
        $this->canControlComments();
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
    
    protected function canControlComments() 
    {
        if ($this->profile->user_id == Yii::app()->user->id) {
            Yii::app()->clientScript->registerScript('rolesForCommentsMarionetteWidget', "
                WebUser.addRoles(['commentsAdmin']);
            ", CClientScript::POS_HEAD);
            
            return true;
        }
        
        return false;
    }
}
