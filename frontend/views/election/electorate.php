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
        'depends' => array('loadmask'),
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
            'aes:views/UserItemView.js'
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

<script id="source-list-tpl" type="text/template">
    <div class="navbar head">
        <div class="navbar-inner">
            <ul class="nav pull-right">
                <li><a id="admins-count"><img class="loader" src="/img/loader-circle-16.gif" style="display: none;">Found <span class="items-count">0</span> persones</a></li>
            </ul>
        </div>
    </div>        

    <div class="main row-fluid">
        <div class="items span8"></div>            
        
        <div class="search-form pull-right span4">
            <form class="well form-vertical">
                <label for="name">Name</label>
                <input type="text" id="PeopleSearch_name" name="name" maxlength="128" class="filter span12">

                <label for="birth_place">Birth Place</label>
                <input type="text" id="PeopleSearch_birth_place" name="birth_place" maxlength="128" class="filter span12">

                <label for="ageFrom">Age from</label>
                <input type="text" id="PeopleSearch_ageFrom" name="ageFrom" class="filter span12">

                <label for="ageTo">Age to</label>
                <input type="text" id="PeopleSearch_ageTo" name="ageTo" class="filter span12">

                <label for="gender">Gender</label>
                <select id="PeopleSearch_gender" name="gender" class="filter span12">
                    <option selected="selected" value="">Any</option>
                    <option value="1">Male</option>
                    <option value="2">Female</option>
                </select>

                <div class="form-actions">
                    <button class="btn btn-primary filter-apply">Search</button>
                    <input type="button" value="Reset" name="reset" class="btn filter-reset">
                </div>
            </form>           
        </div>
        
        <div id="load-btn" class="load-btn-cntr"></div>
    </div>
        
</script>