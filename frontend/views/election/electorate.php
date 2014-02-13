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

$canUserManage = (int)Yii::app()->user->checkAccess('election_manage', array('election'=>$model));

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
                    <option value="2">Famale</option>
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

<script id="user-item-tpl" type="text/template">
        <div class="pull-left">
            <div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">
                <span></span>
                <img alt="<%= profile.displayName %>" src="<%= profile.photoThmbnl64 %>">
            </div>
        </div>

        <div class="pull-right right-top-panel">
            <!-- TODO: move it to subview
            <span class="controls">
                <small>Deprive of powers&nbsp;<i class="icon-remove"></i></small>
            </span>
            
            <span class="controls">
                <small>Empower&nbsp;<i class="icon-plus-sign"></i></small>
            </span>
            
            </% if(empovered) { %>
            <span class="mark">
                <small>Empovered&nbsp;<i class="icon-ok"></i></small>
            </span>
            </% } %> 
            -->
        </div>                        

        <div class="body">
            <a href="<%= profile.pageUrl %>"><%= profile.displayName %></a> <br>

            <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= profile.birth_place %></div>                               
        </div>
</script>