<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

$this->breadcrumbs->add('Nominations', 'userPage/nominations/' . $profile->user_id);

$js = array(
    'aes:collections/FeedCollection.js',
    'aes:views/ItemView.js',
    'aes:views/TableItemView.js',
    'aes:views/MoreView.js',
    'aes:views/FeedCountView.js',
    'aes:views/FeedView.js',
    'aes:models/Election.js',
    'aes:models/Candidate.js',
    'modules/Nominations.js'
);

$this->widget('application.widgets.ClientApp', array(
    'appName' => 'nominations',
    'requires' => array(
        'depends' => array('loadmask'),
        'js' => $js
    )
));

Yii::app()->clientScript->registerScript('starter',
    "App.start({
        userId: ". $userId .",
        canControl: $canControl
     });"
);

?>

<div id="nominations"></div>

<script type="text/template" id="nomination-tpl">
    <h4><a href="<%= UrlManager.createUrl('election/view/' + election.id) %>" target="_blank"><%= election.name %></a></h4>
    <div>
        <div class="date-time"><b>Date:</b>&nbsp;<span><%= i18n.date(status_changed_ts, 'full', 'full') %></span></div>
    </div>
    <div>
        <div class="span11"><b>Status:</b>&nbsp;<%= t(statusText) %></span>&nbsp;
        <span class="controls"><b>
            <a href="#" class="text-success accept-btn">Accept</a>&nbsp;
            <a href="#" class="text-error decline-btn">Decline</a>
        </b></span></div>
        <div class="rates-container">
            <span>&nbsp;</span>
        </div>
    </div>
</script>

<script type="text/template" id="nominations-feed-tpl">
<div class="navbar head">
    <div class="navbar-inner">
        <form class="navbar-search pull-left span4">

            <div class="input-append span12">

                <input type="text" placeholder="Type election title" class="span12" name="name">
                <button class="btn filter-apply">Find</button>
                <button class="btn filter-reset">Reset filter</button>
            </div>

        </form>    
    
        <ul class="nav pull-right">
            <li><a id="items-count"><img class="loader" src="/img/loader-circle-16.gif" style="display: none;">Found <span class="items-count">0</span> </a></li>
        </ul>
    </div>
</div>     

<div class="filter-container"></div>

<div class="nomination-items items span8 offset2"></div>

<div id="load-btn" class="load-btn-cntr"></div>
</script>

<script type="text/template" id="nominations-layout">
    <div id="nominations-feed-container"></div>
</script>