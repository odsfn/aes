<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
$this->breadcrumbs->add('Votes', 'userPage/votes/' . $profile->user_id);

$this->createWidget('CommentsMarionetteWidget')->register();

$js = array(
    'aes:collections/FilterableCollection.js',
    'aes:collections/FeedCollection.js',
    'aes:views/ItemView.js',
    'aes:views/FormView.js',
    'aes:views/TableItemView.js',
    'aes:views/MoreView.js',
    'aes:views/FeedCountView.js',
    'aes:views/FeedView.js',    
    'aes:models/Election.js',
    'modules/UsersVotes.js'
);

$this->widget('application.widgets.ClientApp', array(
    'appName' => 'votes',
    'requires' => array(
        'depends' => array('loadmask', 'backbone.validation'),
        'js' => $js
    )
));

Yii::app()->clientScript->registerScript('starter',
    "App.start({
        userId: ". $userId ."
     });"
);

?>

<div id="votes"></div>

<script type="text/template" id="election-tpl">
    <div class="election">
        <h4><a href="<%= UrlManager.createUrl('election/view/' + id) %>"><%= name %></a></h4>
        <div>
            <b>Status:</b>&nbsp;<span><%= textStatus %></span>
        </div>
    </div>
    <div class="votes-container"></div>
</script>

<script type="text/template" id="voted-candidate-tpl">
    <div>
        <b>Vote for the candidate <a href="<%= UrlManager.createUrl('election/candidates/' + election_id + '/details/' + candidate_id) %>">№<%= electoral_list_pos %>&nbsp;<%= profile.displayName %></a></b>
    </div>
    <div class="last">
        <div class="span11">
        <% if(vote_declined || vote_revoked) { %>
            <span class="label label-important declined-marker" title="<% if(vote_declined) { %>Declined by candidate<% } else { %>Revoked by elector<% } %>">
                <i class="icon-ban-circle"></i>&nbsp;<% if(vote_declined) { %>Declined<% } else { %>Revoked<% } %>
            </span>&nbsp;
        <% } else { %>
            <span class="label label-success declined-marker">
                <i class="icon-ok"></i>&nbsp;Given
            </span>&nbsp;
        <% } %>
            <span class="muted"><%= i18n.date(vote_date, 'full', 'full') %></span>
        </div>
        <div class="rates-container span1">
            <span>&nbsp;</span>
        </div>
    </div>
    <div class="comments-container"></div>
    <hr>
</script>
 
<script type="text/template" id="votes-layout">
    <div id="votes-feed-container"></div>
</script>