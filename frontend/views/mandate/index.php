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

Yii::app()->bootstrap->registerAssetCss('bootstrap-box.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/layouts/core.css');

$this->createWidget('application.widgets.UsersPhoto')->registerCss();
?>

<div class="row-fluid">
    <div id="mandates" class="span10 offset1">
        
    </div>
</div>

<script type="text/template" id="mandates-list-layout">
    <div id="mandates-feed-container"></div>
    <div id="mandate-details"></div>
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
        <h4><a href="details/<%= id %>" class="route"><%= name %></a></h4>
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

<script type="text/template" id="mandate-detailed-tpl">
    <div class="pull-left span1">
        <div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">
            <span></span>
            <img alt="<%= candidate.profile.displayName %>" src="<%= candidate.profile.photoThmbnl64 %>">
        </div>
    </div>
    
    <div class="span11">
        <div>
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

<script type="text/template" id="mandate-details-layout-tpl">    
    <div class="bootstrap-widget" id="title">
        <div class="bootstrap-widget-header smooth">
            <i class="icon-briefcase"></i>
            <h3>
                <ul class="breadcrumbs breadcrumb">
                    <li><a href="" class="route">Mandates</a><span class="divider">/</span></li>
                </ul>
            </h3>
        </div>
    </div>    
    
    <div class="row-fluid">
        <div id="mandate-info" class="span12"></div>
    </div>
    
    <div class="row-fluid">
        <div class="tabs-container">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#electors-tab">Electors</a></li>
                <li><a href="#petitions-tab">Petitions</a></li>
            </ul>

            <div class="tab-content">

                <div id="electors-tab" class="tab-pane active"></div>

                <div id="petitions-tab"></div>

            </div>    
        </div>
    </div>
</script>

<script type="text/template" id="electorfeed-item-tpl">
    <div class="pull-left">
        <div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">
            <span></span>
            <img alt="<%= profile.displayName %>" src="<%= profile.photoThmbnl64 %>">
        </div>
    </div>                       

    <div class="body">
        <a href="<%= profile.pageUrl %>" target="_blank"><%= profile.displayName %></a> <br>

        <div><b>Birth Day: </b><%= i18n.date(profile.birth_day, 'full') %></div>

        <div><b>Birth Place: </b><%= profile.birth_place %></div>                        
    </div>
</script>