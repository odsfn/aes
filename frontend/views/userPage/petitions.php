<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
$this->breadcrumbs->add('Petitions', 'userPage/petitions/' . $profile->user_id);

$js = array(
    'aes:collections/FeedCollection.js',
    'aes:views/ItemView.js',
    'aes:views/FormView.js',
    'aes:views/TableItemView.js',
    'aes:views/MoreView.js',
    'aes:views/FeedCountView.js',
    'aes:views/NoItemView.js',
    'aes:views/FeedView.js',
    'aes:models/Petition.js',
    'aes:collections/Petitions.js',
    'aes:views/PetitionView.js',
    'aes:views/PetitionsFeedView.js',
    'aes:views/TabsView.js'
);

$this->widget('application.widgets.ClientApp', array(
    'appName' => 'petitions',
    'requires' => array(
        'depends' => array('loadmask', 'backbone.validation'),
        'js' => $js
    )
));

$this->createWidget('RatesMarionetteWidget')->register();

Yii::app()->clientScript->registerScript('starter',
    "App.start({
        user_id: ". $profile->user_id .",
        canRate: ". (($profile->user_id == Yii::app()->user->id) ? 1 : 0) .",
        mandates: ". $usersMandates ."
     });"
);

?>

<div id="petitions"></div>

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

<div class="petitions-items items"></div>

<div id="load-btn" class="load-btn-cntr"></div>
</script>

<script type="text/template" id="petition-tpl">
    <div class="petition">
        <h4><a href="<%= UrlManager.createUrl('mandate/index/details/' + mandate_id + '/petition_' + id) %>" class="route"><%= title %></a></h4>
        <% if (shortContent != false) { %>
        <p class="short-text"><%= shortContent %></p>
        <% } else { %>
        <p class="text"><%= content %></p>
        <% } %>
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