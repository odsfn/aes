<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

$this->breadcrumbs->add('Admins', '/election/admins/' . $model->id);
$this->breadcrumbs->add($model->name, '/election/view/' . $model->id);

$this->widget('application.widgets.ClientApp', array(
//    'isolated' => true,
    'appName' => 'admins',
    'requires' => array(
        'depends' => array('loadmask'),
        'js' => array(
            'aes:collections/FeedCollection.js',
            'aes:views/ItemView.js',
            'aes:views/MoreView.js',
            'aes:views/FeedCountView.js',
            'modules/AdminsManagement.js'
         )
    )
));

$canUserManage = (int)Yii::app()->user->checkAccess('election_manageAdmins', array('election'=>$model));

Yii::app()->clientScript->registerScript('starter',
    "App.module('AdminsManagement').setOptions({
        electionId: ". $model->id .",
        canDeprive: $canUserManage ,
        canEmpover: $canUserManage    
     }); 
     App.start();"
);

$this->createWidget('application.widgets.UsersPhoto')->registerCss();
?>

<div id="admins"></div>

<script id="admins-layout-tpl" type="text/template">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#all-admins-tab" data-toggle="tab">All admins</a></li>
        <li id="invite-tab-sel"><a href="#invite-tab" data-toggle="tab">Invite</a></li>
    </ul>
    
    <div class="tab-content">
        <div id="all-admins-tab" class="tab-pane active"></div>
        
        <div id="invite-tab" class="tab-pane"></div> 
    </div>
</script>

<script id="admins-list-tpl" type="text/template">
    <div class="navbar head">
        <div class="navbar-inner">
            <form class="navbar-search pull-left span4">

                <div class="input-append span12">

                    <input type="text" placeholder="Type name" class="span12" name="userName">
                    <button class="btn userName-filter-apply">Find</button>
                    <button class="btn filter-reset">Reset filter</button>
                </div>

            </form>

            <ul class="nav pull-right">
                <li><a id="admins-count"><img class="loader" src="/img/loader-circle-16.gif" style="display: none;">Found <span class="items-count">0</span> persones</a></li>
            </ul>
        </div>
    </div>        

    <div class="items">
    </div>            

    <div id="load-btn" class="load-btn-cntr">
    </div>    
</script>

<script id="admin-item-tpl" type="text/template">
        <div class="pull-left">
            <div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">
                <span></span>
                <img alt="<%= profile.displayName %>" src="<%= profile.photoThmbnl64 %>">
            </div>
        </div>

        <div class="pull-right">
            <span class="controls">
                <small>Deprive of powers&nbsp;<i class="icon-remove"></i></small>
            </span>
        </div>                        

        <div class="body">
            <a href="<%= profile.pageUrl %>"><%= profile.displayName %></a> <br>

            <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= profile.birth_place %></div>

            <div><b>Role: </b><%= t(authAssignment.itemname) %></div>                                
        </div>
</script>

<script id="user-item-tpl" type="text/template">
        <div class="pull-left">
            <div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">
                <span></span>
                <img alt="<%= displayName %>" src="<%= photoThmbnl64 %>">
            </div>
        </div>

        <div class="pull-right">
            <span class="controls">
                <small>Empower&nbsp;<i class="icon-plus-sign"></i></small>
            </span>
            <% if(empovered) { %>
            <span class="mark">
                <small>Empovered&nbsp;<i class="icon-ok"></i></small>
            </span>
            <% } %>
        </div>                        

        <div class="body">
            <a href="<%= pageUrl %>"><%= displayName %></a> <br>

            <div><b>Birth Day: </b><%= i18n.date(birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= birth_place %></div>                               
        </div>
</script>

<script type="text/template" id="no-item-tpl">
    There is no items.
</script>