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
    'modules/PetitionsList.js',
    'modules/MandateDetails.js',
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
$this->createWidget('CommentsMarionetteWidget')->register();
$this->createWidget('application.widgets.UsersPhoto')->registerCss();
?>

<div class="row-fluid">
    <div id="mandates" class="span10 offset1">
        
    </div>
</div>

<div id="create-petition" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="create-petition-label" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="create-petition-label">Create new petition</h3>
    </div>
    <div class="modal-body">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
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
    
    <div id="mandate-tabs" class="row-fluid">
        <div class="tabs-container">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#electors-tab">Electors</a></li>
                <li><a data-toggle="tab" href="#petitions-tab">Petitions</a></li>
                <!--<li class="petition-create-btn"><a href="<%= UrlManager.createUrl('petition/create/mandateId/' + App.module('MandateDetails').getActiveMandate().get('id')) %>">Create new petition</a></li>-->
                <li class="petition-create-btn"><a data-toggle="tab" href="#create-petition-tab">Create new petition</a></li>
            </ul>

            <div class="tab-content">

                <div id="electors-tab" class="tab-pane active"></div>

                <div id="petitions-tab" class="tab-pane"></div>
                
                <div id="create-petition-tab" class="tab-pane">
                    <?= $this->renderPartial('frontend.views.petition._form', array('model'=>new Petition, 'forAjax' => true)); ?>
                </div>
                
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

<script type="text/template" id="petitions-list-layout-tpl">
    <div id="petitions-feed-container"></div>
    <div id="petitions-details"></div>
</script>

<script type="text/template" id="petitions-feed-tpl">
<div class="navbar head">
    <div class="navbar-inner">
        <div class="top-filter-container"></div>
        <ul class="nav pull-right">
            <li><a id="items-count"><img class="loader" src="/img/loader-circle-16.gif" style="display: none;">Found <span class="items-count">0</span> </a></li>
        </ul>
    </div>
</div>     

<div class="filter-container"></div>

<div class="petitions-items items span9"></div>

<div id="load-btn" class="load-btn-cntr"></div>
</script>

<script type="text/template" id="petition-tpl">
    <div class="petition">
        <h4><a href="petition_details/<%= id %>" class="route"><%= title %></a></h4>
        <p class="short-text"><%= shortContent %></p>
        <div class="details row-fluid">
            <div class="person-photo span1">
                <div class="img-wrapper-tocenter users-photo">
                    <span></span>
                    <a href="<%= person.pageUrl %>"><img src="<%= person.photoThmbnl64 %>" alt="<%= person.displayName %>"></a>
                </div>
            </div>
            <div class="span5">
                <h5><a href="<%= person.pageUrl %>" target="_blank"><%= person.displayName %></a></h5>
                <p>
                    <b><% if(personType == 'creator') { %>Created<% } else { %>Addressed<% } %>:</b> 
                    <span><%= i18n.date(created_ts, 'full', 'full') %></span>
                </p>
            </div>
            <div class="support span2 pull-right">
                <div class="petition-rates pull-right"></div>
            </div>
        </div>
    </div>
    <hr>
</script>

<?php $this->renderPartial('frontend.views.jsTemplates.petitionRates'); ?>