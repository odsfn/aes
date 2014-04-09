<?php

$js = array(
    'aes:collections/FeedCollection.js',
    'aes:views/ItemView.js',
    'aes:views/TableItemView.js',
    'aes:views/MoreView.js',
    'aes:views/FeedCountView.js',
    'aes:views/FormView.js',
    'aes:views/NoItemView.js',
    'aes:views/FeedView.js',
    'modules/MandatesList.js'
);

$this->widget('application.widgets.ClientApp', array(
    'appName' => 'mandates',
    'requires' => array(
        'depends' => array('loadmask', 'backbone.validation'),
        'js' => $js
    )
));

Yii::app()->clientScript->registerScript('starter',
    "App.start({});"
);

$this->createWidget('application.widgets.UsersPhoto')->registerCss();
?>

<div class="row-fluid">
    <div id="mandates" class="span10 offset1">
        
    </div>
</div>

<script type="text/template" id="mandates-list-layout">
    <div id="mandates-feed-container"></div>
</script>

<script type="text/template" id="mandates-feed-tpl">
<div class="navbar head">
    <div class="navbar-inner">
        <div class="top-filter-container"></div>
        <ul class="nav pull-right">
            <li><a id="items-count"><img class="loader" src="/img/loader-circle-16.gif" style="display: none;">Found <span class="items-count">0</span> </a></li>
        </ul>
    </div>
</div>     

<div class="filter-container"></div>

<div class="nomination-items items span8"></div>

<div id="load-btn" class="load-btn-cntr"></div>
</script>

<script type="text/template" id="mandate-tpl">
    <div class="mandate">
        <h4><a href="viewDetails/<%= id %>" class="route"><%= name %></a></h4>
        <div>
            <div><b>Owner</b>: <%= candidate.profile.displayName %></div>
            <div class="election-name"><b>Election name: </b><%= election.name %></div>
            <div class="date-time">
                <b>Validity from</b>&nbsp;
                <span><%= i18n.date(submiting_ts, 'full') %></span>&nbsp;<b>to</b>
                <span><%= i18n.date(expiration_ts, 'full') %></span>
            </div>
            <div class="status"><b>Status:</b>&nbsp;<%= statusText %></span>&nbsp;</div>
        </div>
    </div>
</script>