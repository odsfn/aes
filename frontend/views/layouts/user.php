<?php 
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/layouts/user.css');

$username = $this->profile->username;
$userPageUrl = array('/userPage', 'id'=>$this->profile->user_id);

$this->breadcrumbs->add($username, $userPageUrl);

$this->clips['titleIcon'] = '<i class="icon-user"></i>';
$this->clips['titleAfterBredcrumbsContent'] = '<h3 class="pull-right">Online</h3>';

$this->beginClip('mainPicture');

$this->widget('application.widgets.UsersPhoto', array(
    'user' => $this->profile,
    'imgWidth' => 237,
    'imgHeight'=> 300,
    'containerWidth' => '20%',
    'containerHeight' => '300px'
)); 

$this->endClip(); 


$this->beginClip('navigation');

$this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'pills',
    'stacked' => 'true',
    'items' => array(
        array('label'=> Yii::t('userPage', ($this->self) ? 'My page' : 'Page'), 'url'=> array('/userPage/index', 'id'=>$this->profile->user_id)),
        array('label'=> Yii::t('userPage', 'My messages'), 'url'=> array('/messaging/index'), 'visible' => $this->self, 'itemOptions' => array('class'=>'messaging')),
        array('label'=> Yii::t('userPage', 'Write message'), 'url'=> array('/messaging/index/chat_with/' . $this->profile->user_id), 'visible' => (!Yii::app()->user->isGuest && !$this->self)),
        array('label'=> Yii::t('userPage', ($this->self) ? 'My votes' : 'Votes'), 'url'=> array('/userPage/votes', 'id'=>$this->profile->user_id)),
        array('label'=> Yii::t('userPage', ($this->self) ? 'My nominations' : 'Nominations'), 'url'=> array('/userPage/nominations', 'id'=>$this->profile->user_id)),
        array('label'=> Yii::t('userPage', ($this->self) ? 'My mandates' : 'Mandates'), 'url'=> array('/userPage/mandates', 'id'=>$this->profile->user_id)),
        array('label'=> Yii::t('userPage', ($this->self) ? 'My petitions' : 'Petitions'), 'url'=> array('/userPage/petitions', 'id'=>$this->profile->user_id)),
        array('label'=> Yii::t('userPage', ($this->self) ? 'My videos' : 'Videos'), 'url'=> array('/userPage/videos', 'id'=>$this->profile->user_id)),
    )
));

$this->widget('album.widgets.TinyPreview', array(
    'targetId' => $this->profile->target_id,
    'isModuleSelected' => $this->id == 'image' || $this->action->id == 'photos',
    'albumRoute' => '/userPage/photos/' . $this->profile->target_id
)); 

$this->endClip();


$this->beginContent('//layouts/core');

echo $content;

$this->endContent();
?>

