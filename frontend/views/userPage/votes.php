<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
$this->breadcrumbs->add('Votes', 'userPage/votes/' . $profile->user_id);

$js = array(
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

$this->createWidget('CommentsMarionetteWidget')->register();

Yii::app()->clientScript->registerScript('starter',
    "App.start({
        userId: ". $userId ."
     });"
);

?>

<div id="votes"></div>

<script type="text/template" id="election-tpl">
    <div class="election">
        <h4><a href="<%= UrlManager.createUrl('election/view/' + id) %>" target="_blank"><%= name %></a></h4>
        <div>
            <b>Status:</b>&nbsp;<span><%= textStatus %></span>
        </div>
    </div>
    <div class="votes-container"></div>
</script>

<script type="text/template" id="voted-candidate-tpl">
    <h5>Vote for the candidate</h5>
    <b><a href="<%= UrlManager.createUrl('election/candidates/' + election_id + '/details/' + id) %>" target="_blank">№<%= electoral_list_pos %>&nbsp;<%= profile.displayName %></a></b>
    <div class="row-fluid">
        <div class="span5"><span class="muted">Given: <%= i18n.date(vote_date, 'full', 'full') %></span></div>
        <div class="span5"><% if(vote_declined) { %><span class="label label-important declined-marker"><i class="icon-ban-circle"></i>&nbsp;Declined</span><% } %></span>
        <div class="rates-container">
            <span>&nbsp;</span>
        </div>
    </div>
    <div class="comments-container"></div>
</script>
 
<script type="text/template" id="votes-layout">
    <div id="votes-feed-container"></div>
</script>