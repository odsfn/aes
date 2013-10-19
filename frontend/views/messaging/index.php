<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
$this->breadcrumbs->add('Messages', '/messaging/index');

$this->widget('application.widgets.ClientApp', array(
//    'isolated' => true,
    'appName' => 'messaging',
    'requires' => array(
        'depends' => array('loadmask', 'backbone.poller'),
        'js' => array(
            'models/Conversation.js',
            'models/Message.js',
            'models/Participant.js',
            'aes:collections/FeedCollection.js',
            'collections/Conversations.js',
            'collections/Messages.js',
            'aes:views/MoreView.js',
            'aes:views/FeedCountView.js',
            'modules/Messaging.js',
            'modules/Chat.js',
            'modules/LivePolling.js',
            'modules/UnviewedIndicator.js'
         )
    )
));

// @TODO: Replace this by setting system property
if(defined('TEST_APP_INSTANCE') && TEST_APP_INSTANCE) {
    Yii::app()->clientScript->registerScript('setLimitTest', 
            "App.on('initialize:before', function() { 
                App.module('Messaging').setOptions({
                    convsLimit: 4
                });

                App.module('Messaging.Chat').setOptions({
                    messagesLimit: 4
                });
                
                App.module('Messaging.LivePolling').setOptions({
                    poller: {
                        delay: 6000
                    }
                });
            });",
            CClientScript::POS_HEAD
    );                    
}

?>

<script id="messaging-layout" type="text/template">
    <audio id="message-in" preload style="display: none;">
        <source src="/audio/message-in.mp3" type="audio/mpeg">
        <source src="/audio/message-in.wav" type="audio/wav">
    </audio>
    
    <ul class="nav nav-tabs">
        <li class="active"><a href="#conversations-tab">Conversations <span class="text-warning"></span></a></li>
        <li><a href="#active-conv-tab">View active <span class="text-warning"></span></a></li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane active" id="conversations-tab">
            <div class="navbar head">
                <div class="navbar-inner">
                    <form class="navbar-search pull-left span4">
                       
                        <div class="input-append span12">
                            
                            <input name="participantName" type="text" class="span12" placeholder="Type name of companion">
                            <button class="btn participant-filter-apply">Find</button>
                            
                        </div>
                        
                    </form>

                    <!-- <button class="btn unviewed-filter">Unviewed</button> -->
                    
                    <ul class="nav pull-right">
                        <li><a id="convs-count"><img src="/img/loader-circle-16.gif" class="loader">Found <span>0</span> conversations</a></li>
                    </ul>
                </div>
            </div>
            
            <div id="convs-container"></div>
            <div id="convs-load-btn"></div>
        </div>

        <div class="tab-pane" id="active-conv-tab">
            <div class="active-chat-titles-cnt"><img src="/img/loader-circle-16.gif" class="loader"></div>
            <div class="active-chat-cnt"></div>
        </div>

    </div>
</script>

<script id="conversation" type="text/template">
    <div class="media post">
        <div class="pull-left">
            <div class="img-wrapper-tocenter users-photo users-photo-<%= participant.user_id %>">
                <span></span>
                <a href="#"><img src="<%= participant.photoThmbnl64 %>" alt="<%= participant.displayName %>"></a>
            </div>
        </div>

        <div class="media-body">
            <div class="post-body">
            
                <h5 class="media-heading">
                    With <span class="user"><a href="<%= participant.pageUrl %>" target="_blank"><%= participant.displayName %></a></span>
                    <small>Last message from <%= lastMessage.user.displayName %> <a href="#<%= lastMessage.id %>"><%= i18n.date(created_ts, 'full', 'full') %> <% if(title && title !== '') { %>at "<%= title %>"<% }; %></a></small>
                    <i class="icon-bell pull-right <% if(hasUnviewedIncome) { %>visible<%}; %>"></i>
                </h5>
                
                <div class="post-content">
                    <%= lastMessage.text %>
                </div>

            </div>
        </div>
    </div>
</script>

<script id="message-tpl" type="text/template">
        <div class="pull-left">
            <div class="img-wrapper-tocenter users-photo users-photo-<%= user_id %>">
                <span></span>
                <a><img src="<%= user.photoThmbnl64 %>" alt="<%= user.displayName %>"></a>
            </div>
        </div>

        <div class="media-body">
            <div class="post-body">
            
                <h5 class="media-heading">
                    <span class="user"><%= user.displayName %></span> 
                    <small><a><%= i18n.date(created_ts, 'full', 'full') %></a></small>
                    <i class="icon-bell pull-right"></i>
                </h5>

                <div class="post-content">
                    <%= text %>
                </div>

            </div>
        </div>
</script>

<script type="text/template" id="more-btn-tpl">
    <div class="row-fluid get-more">
	<div class="span12"><a><%= t(view.moreMsg) %></a><span><img src="/img/loader-circle-16.gif" class="loader" />Loading...</span></div>
    </div>
</script>

<script type="text/template" id="chat-layout-tpl">
        <div class="navbar head">
            <div class="navbar-inner">                
                <ul class="nav pull-right">
                    <li><a class="msgs-count"><img src="/img/loader-circle-16.gif" class="loader">Total Messages Count: <span>0</span></a></li>
                    <li class="load-btn-cnt"></li>
                </ul>
            </div>
        </div>    
    
        <div class="messages-cnt"></div>

        <div class="new-post row-fluid">
        
            <div class="participant">
                <div class="img-wrapper-tocenter users-photo"><span></span><img alt="<%= user.displayName %>" src="<%= user.photoThmbnl64 %>"></div>
            </div>        
        
            <div class="body span9 well">
                <textarea class="span12"></textarea>
                <div class="controls pull-right">
                    <button data-loading-text="<%= t('Sending...') %>" class="btn btn-primary post" type="button"><%= t('Send') %></button>
                    <!--<button class="btn" type="button">Attach</button>-->
                </div>
            </div>

            <div class="participant">
                <div class="img-wrapper-tocenter users-photo"><span></span><img alt="<%= participant.displayName %>" src="<%= participant.photoThmbnl64 %>"></div>
            </div>    
            
        </div>    
</script>

<script type="text/template" id="load-msg-btn-tpl">
    <button class="btn more"><%= t(view.moreMsg) %></button>
</script>

<script type="text/template" id="chat-title-tpl">
    <a href="#">
        <span class="text-warning new-count">+<span><%= unviewedCount %></span>&nbsp;</span><span class="title"><%= title %></span>&nbsp;<i class="icon-remove"></i>
    </a>
</script>

<script type="text/template" id="msgs-in-count-tpl">
    <span><b>&nbsp+<%= count %></b></span>
</script>

<script type="text/template" id="no-item-tpl">
    <span><%= t('No items found') %>.</span>
</script>