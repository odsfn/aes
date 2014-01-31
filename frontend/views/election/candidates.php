<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

$this->breadcrumbs->add('Candidates', '/election/candidates/' . $model->id);
$this->breadcrumbs->add($model->name, '/election/view/' . $model->id);

$js = array(
    'aes:collections/FeedCollection.js',
    'aes:views/MoreView.js',
    'aes:views/FeedCountView.js',
    'modules/Candidates.js',
    'modules/Details.js'
);

$canUserManage = (int)Yii::app()->user->checkAccess('election_administration', array('election'=>$model));
$canUserVote = (int)Yii::app()->user->getId();

if($canUserManage)
    $js[] = 'modules/Invite.js';

$this->widget('application.widgets.ClientApp', array(
//    'isolated' => true,
    'appName' => 'candidates',
    'requires' => array(
        'depends' => array('loadmask'),
        'js' => $js
    )
));

Yii::app()->clientScript->registerScript('starter',
    "App.module('Candidates').setOptions({
        electionId: ". $model->id .",
        canInvite: $canUserManage," 
        . ( (!empty($candidate)) ? "currentCandidateId: {$candidate->id}," : "" ) .
        "canVote: $canUserVote
     }); 
     App.start();"
);

$this->createWidget('application.widgets.UsersPhoto')->registerCss();
?>

<div id="candidates"></div>

<div id="candidate-details">Here is the candidates details</div>

<script id="cands-layout-tpl" type="text/template">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#electoral-list-tab" data-toggle="tab">Electoral list</a></li>
        <li><a href="#all-cands-tab" data-toggle="tab">All candidates</a></li>
        <li id="invite-tab-sel"><a href="#invite-tab" data-toggle="tab">Invite</a></li>
    </ul>
    
    <div class="tab-content">
    
        <div id="electoral-list-tab" class="tab-pane active"></div>
    
        <div id="all-cands-tab" class="tab-pane"></div>
        
        <div id="invite-tab" class="tab-pane"></div> 
        
    </div>
</script>

<script id="cands-list-tpl" type="text/template">
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
                <li><a id="items-count"><img class="loader" src="/img/loader-circle-16.gif" style="display: none;">Found <span class="items-count">0</span> </a></li>
            </ul>
        </div>
    </div>        

    <div class="items">
    </div>            

    <div id="load-btn" class="load-btn-cntr">
    </div>    
</script>

<script id="electoral-list-item-tpl" type="text/template">
        <div class="pull-left">
            <div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">
                <span></span>
                <img alt="<%= profile.displayName %>" src="<%= profile.photoThmbnl64 %>">
            </div>
        </div>

        <div class="pull-right vote-cntr"></div>                   

        <div class="body">
            <a href="details/<%= id %>" class="route"><%= profile.displayName %></a> <b>№<%= electoral_list_pos %></b> <br>

            <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= profile.birth_place %></div>

            <div><b>Status: </b> <%= t(statusText) %></div>                                
        </div>
</script>

<script id="cand-list-item-tpl" type="text/template">
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
            <a href="details/<%= id %>" class="route"><%= profile.displayName %></a> <% if(electoral_list_pos) { %> <b>№<%= electoral_list_pos %></b> <% } %><br>

            <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= profile.birth_place %></div>

            <div><b>Status: </b> <%= t(statusText) %></div>                                
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
                <small>Invite&nbsp;<i class="icon-plus-sign"></i></small>
            </span>
            <% if(invited) { %>
            <span class="mark">
                <small>Invited&nbsp;<i class="icon-ok"></i></small>
            </span>
            <% } %>
        </div>                        

        <div class="body">
            <a href="<%= pageUrl %>"><%= displayName %></a> <br>

            <div><b>Birth Day: </b><%= i18n.date(birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= birth_place %></div>                               
        </div>
</script>

<script id="votefeed-item-tpl" type="text/template">
        <div class="pull-left">
            <div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">
                <span></span>
                <img alt="<%= profile.displayName %>" src="<%= profile.photoThmbnl64 %>">
            </div>
        </div>

        <div class="pull-right">
            <% if(status !== 1) { %>
            <span class="controls">
                <small>Decline&nbsp;<i class="icon-remove-sign"></i></small>
            </span>
            <% } %>
            
            <% if(status === 1) { %>
            <span class="mark">
                <small>Declined&nbsp;<i class="icon-remove-sign"></i></small>
            </span>
            <% } %>
        </div>                        

        <div class="body">
            <a href="<%= profile.pageUrl %>"><%= profile.displayName %></a> <br>

            <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= profile.birth_place %></div>                               
        </div>
</script>

<script type="text/template" id="more-btn-tpl">
    <div class="row-fluid get-more">
	<div class="span12"><a><%= t(view.moreMsg) %></a><span><img src="/img/loader-circle-16.gif" class="loader" />Loading...</span></div>
    </div>
</script>

<script type="text/template" id="no-item-tpl">
    There is no items.
</script>

<script type="text/template" id="candidate-detailed">
        <div class="pull-left">
            <div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">
                <span></span>
                <img alt="<%= profile.displayName %>" src="<%= profile.photoThmbnl64 %>">
            </div>
            
            <div class="status-controls">
                <button class="btn">Foo bar</button>
            </div>
        </div>

        <div class="pull-right vote-cntr"></div>

        <div class="body">
            <a href="<%= profile.pageUrl %>" target="_blank"><%= profile.displayName %></a> <b>№<%= electoral_list_pos %></b> <br>

            <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= profile.birth_place %></div>

            <div><b>Status: </b> <%= t(statusText) %></div>
            
            <div><b>Votes count: </b> 1234</div>
        </div>
</script>

<script type="text/template" id="candidate-details-layout">
    <div id="candidate-info"></div>
    
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li><a href="#docs-tab">Documents</a></li>
            <li class="active"><a href="#votes-tab" data-toggle="tab">Votes</a></li>
            <li><a href="#mandates-tab"></a></li>
        </ul>

        <div class="tab-content">
            
            <div id="docs-tab"></div>
        
            <div id="votes-tab" class="tab-pane active"></div>
            
            <div id="mandates-tab"></div>

        </div>    
    </div>
</script>

<script type="text/template" id="vote-box-tpl"> 
    <div class="checkbox vote <% if(!active) { %>inactive<% } %>">
        <span class="value"><% if(voted && !declined) { %>&#10003;<%} else if(declined) { %>&#10007;<% } else { %> <% } %></span>
    </div>
</script>