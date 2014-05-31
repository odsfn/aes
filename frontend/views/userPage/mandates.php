<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
$this->breadcrumbs->add('Mandates', 'userPage/mandates/' . $profile->user_id);

$js = array(
    'aes:collections/FeedCollection.js',
    'aes:views/ItemView.js',
    'aes:views/FormView.js',
    'aes:views/TableItemView.js',
    'aes:views/MoreView.js',
    'aes:views/FeedCountView.js',
    'aes:views/FeedView.js',    
    'aes:models/Mandate.js',
    'aes:collections/Mandates.js',
    'modules/Mandates.js'
);

$this->widget('application.widgets.ClientApp', array(
    'appName' => 'mandates',
    'requires' => array(
        'depends' => array('loadmask', 'backbone.validation'),
        'js' => $js
    )
));

$this->createWidget('CommentsMarionetteWidget')->register();

$canControl = false;

if(Yii::app()->user->id && $profile->user_id == Yii::app()->user->id)
    $canControl = true;

Yii::app()->clientScript->registerScript('starter',
    "App.start({
        userId: ". $profile->user_id .",
        canDeleteComments: " . (int)$canControl . "
     });"
);

?>

<div id="mandates"></div>

<script type="text/template" id="nomination-tpl">
    <div class="nomination">
        <h4><a href="<%= UrlManager.createUrl('election/view/' + election.id) %>"><%= election.name %></a></h4>
        <div>
            <div class="date-time"><b>Date:</b>&nbsp;<span><%= i18n.date(status_changed_ts, 'full', 'full') %></span></div>
        </div>
        <div>
            <div class="span status"><b>Status:</b>&nbsp;<%= t(statusText) %></span>&nbsp;
            <span class="controls"><b>
                <a href="#" class="text-success accept-btn">Accept</a>&nbsp;
                <a href="#" class="text-error decline-btn">Decline</a>
            </b></span></div>
            <div class="rates-container">
                <span>&nbsp;</span>
            </div>
        </div>
    </div>
    <div class="comments-container"></div>
</script>

<script type="text/template" id="nominations-feed-tpl">
<div class="navbar head">
    <div class="navbar-inner">
        <div class="top-filter-container"></div>
        <ul class="nav pull-right">
            <li><a id="items-count"><img class="loader" src="/img/loader-circle-16.gif" style="display: none;">Found <span class="items-count">0</span> </a></li>
        </ul>
    </div>
</div>     

<div class="filter-container"></div>

<div class="nomination-items items span8 offset2"></div>

<div id="load-btn" class="load-btn-cntr"></div>
</script>

<script type="text/template" id="mandate-tpl">
    <div class="mandate">
        <h4><a href="<%= UrlManager.createUrl('mandate/index/details/' + id + '/') %>" class="route"><%= name %></a></h4>
        <div>
            <div><b>Owner</b>: <%= candidate.profile.displayName %></div>
            <div class="election-name"><b>Election name: </b><%= election.name %></div>
            <div class="date-time">
                <b>Validity from</b>&nbsp;
                <span><%= i18n.date(submiting_ts, 'full') %></span>&nbsp;<b>to</b>
                <span><%= i18n.date(expiration_ts, 'full') %></span>
            </div>
            <div class="status"><b>Status:</b>&nbsp;<%= statusText %></span>&nbsp;</div>
            <div class="rates-container pull-right">
                <span>&nbsp;</span>
            </div>
        </div>
    </div>
    <div class="comments-container"></div>
    <hr>
</script>

<script type="text/template" id="mandates-layout">
    <div id="mandates-feed-container"></div>
</script>