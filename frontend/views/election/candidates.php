<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

$this->breadcrumbs->add('Candidates', '/election/candidates/' . $model->id);
$this->breadcrumbs->add($model->name, '/election/view/' . $model->id);

$js = array(
    'aes:collections/FilterableCollection.js',
    'aes:collections/FeedCollection.js',
    'aes:views/ItemView.js',
    'aes:views/MoreView.js',
    'aes:views/FeedCountView.js',
    'aes:views/ButtonView.js',
    'aes:views/ModalView.js',
    'aes:models/Election.js',
    'aes:models/Candidate.js',
    'modules/Candidates.js',
    'modules/Details.js'
);

$canUserManage = (int)Yii::app()->user->checkAccess('election_administration', array('election'=>$model));
$canUserVote = (int)Yii::app()->user->checkAccess('election_electing', array('election'=>$model));

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
    "App.start({
        electionId: ". $model->id .",
        canInvite: $canUserManage," 
        . ( (!empty($candidate)) ? "currentCandidateId: {$candidate->id}," : "" ) .
        "canVote: $canUserVote
     });"
);

$this->createWidget('application.widgets.UsersPhoto')->registerCss();
?>

<div id="candidates"></div>

<div id="candidate-details"></div>

<script id="revoke-vote-message" type="text/template">
    <p>Are you really want to revoke your vote?</p>
    <% if(--revoteTriesRemain <= 3 && revoteTriesRemain > 0) { %>
    <p>You will have ability to revote <%= revoteTriesRemain %> times after you revoke this vote.</p>
    <% } else if (revoteTriesRemain == 0) { %>
    <p>Please note, you will not be able to revoke your vote again. This is the last try.</p>
    <% } %>
    <p>You can vote next time during <%= (revoteTime / 60 / 1000) %> minutes.</p>
</script>

<script id="cands-layout-tpl" type="text/template">
    <ul class="nav nav-tabs">
        <li id="electoral-list-tab-sel"><a href="#electoral-list-tab" data-toggle="tab">Electoral list</a></li>
        <li class="active"><a href="#all-cands-tab" data-toggle="tab">All candidates</a></li>
        <li id="invite-tab-sel"><a href="#invite-tab" data-toggle="tab">Invite</a></li>
    </ul>
    
    <div class="tab-content">
    
        <div id="electoral-list-tab" class="tab-pane"></div>
    
        <div id="all-cands-tab" class="tab-pane active"></div>
        
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
            <% if(status === 0) { %>
            <span class="controls">
                <small>Decline&nbsp;<i class="icon-remove-sign"></i></small>
            </span>
            <% } %>
            
            <% if(status === 1) { %>
            <span class="mark">
                <small>Declined&nbsp;<i class="icon-remove-sign"></i></small>
            </span>
            <% } %>
                        
            <% if(status === 2) { %>
            <span class="mark">
                <small>Revoked by elector&nbsp;<i class="icon-remove-sign"></i></small>
            </span>
            <% } %>                        
        </div>                        

        <div class="body">
            <a href="<%= profile.pageUrl %>"><%= profile.displayName %></a> <br>

            <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= profile.birth_place %></div>                               
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
        </div>

        <div class="pull-right vote-cntr"></div>

        <div class="body">
            <a href="<%= profile.pageUrl %>"><%= profile.displayName %></a> <% if(statusText === 'Registered') { %> <b>№<%= electoral_list_pos %></b> <% } %> <br>

            <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

            <div><b>Birth Place: </b><%= profile.birth_place %></div>

            <div><b>Status: </b> <%= t(statusText) %></div>
            
            <% if(statusText === 'Registered' && (electionStatusText == 'Election' || electionStatusText == 'Finished')) { %>
            <div><b>Accepted votes count: </b> <%= votesCount %></div>
            <% } %>
        </div>
</script>

<script type="text/template" id="candidate-details-layout">
    
    <div class="row-fluid">
        <div id="candidate-info" class="span6"></div>
        <div id="controls" class="pull-right span6"></div>
    </div>
    
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li><a href="#docs-tab">Documents</a></li>
            <li id="details-votes-tab-sel"><a href="#votes-tab" data-toggle="tab">Votes</a></li>
            <li id="mandates-tab-sel"><a href="#mandates-tab" data-toggle="tab">Mandate</a></li>
        </ul>

        <div class="tab-content">
            
            <div id="docs-tab"></div>
        
            <div id="votes-tab" class="tab-pane"></div>
            
            <div id="mandates-tab" class="tab-pane"></div>

        </div>    
    </div>
</script>

<script type="text/template" id="vote-box-tpl"> 
    <div class="checkbox vote <% if(!active) { %>inactive<% } %>">
        <span class="value"><% if(voted && !declined) { %>&#10003;<%} else if(declined) { %>&#10007;<% } else { %> <% } %></span>
    </div>
</script>