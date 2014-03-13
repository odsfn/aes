<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

$this->breadcrumbs->add('Electorate', '/election/electorate/' . $model->id);
$this->breadcrumbs->add($model->name, '/election/view/' . $model->id);

$this->widget('application.widgets.ClientApp', array(
//    'isolated' => true,
    'appName' => 'electorate',
    'requires' => array(
        'depends' => array('loadmask', 'backbone.validation'),
        'js' => array(
            'aes:models/User.js',
            'aes:collections/FeedCollection.js',
            'aes:collections/Users.js',
            'aes:views/ItemView.js',
            'aes:views/MoreView.js',
            'aes:views/FeedCountView.js',
            'aes:views/NoItemView.js',
            'aes:views/TableItemView.js',
            'aes:views/FeedView.js',
            'aes:views/UserItemView.js',
            'aes:views/FormView.js'
         )
    )
));

$canUserManage = (int)(Yii::app()->user->checkAccess('election_manage', array('election'=>$model)) && $model->status == Election::STATUS_REGISTRATION);

Yii::app()->clientScript->registerScript('starter',
    "App.start({
        electionId: ". $model->id .",
        canInvite: $canUserManage    
     });"
);

$this->createWidget('application.widgets.UsersPhoto')->registerCss();
?>

<script id="collect-layout-tpl" type="text/template">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#dest-tab" data-toggle="tab">Electorate</a></li>
        <li id="source-tab-sel"><a href="#source-tab" data-toggle="tab">Invite</a></li>
    </ul>
    
    <div class="tab-content">
        <div id="dest-tab" class="tab-pane active"></div>
        
        <div id="source-tab" class="tab-pane"></div>
    </div>
</script>