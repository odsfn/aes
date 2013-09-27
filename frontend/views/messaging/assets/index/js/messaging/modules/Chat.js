/** 
 * Chat module. 
 * 
 * Controls chat views
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Messaging.Chat', function(Chat, App, Backbone, Marionette, $, _) {
    
    var MessageView = Marionette.ItemView.extend({
        
        className: 'media post',
        
        template: '#message-tpl',

        initialize: function(options) {
            this.conversation = options.conversation;
        },
        
        serializeData: function() {
            return _.extend(Marionette.ItemView.prototype.serializeData.apply(this), {
               user: this.conversation.getUserData(this.model.get('user_id'))
            });
        }
    });    
    
    var MessagesView = Marionette.CollectionView.extend({
        itemView: MessageView,
                
        initialize: function(options) {
            this.conversation = options.conversation;
        },
                
        itemViewOptions: function(model, index) {
            return {
                conversation: this.conversation
            };
        }
    });
    
    var ConversationTitleView;
    
    var InputView;
    
    var LoadMsgsBtnView;
    
//    var FiltersView;
    
    var ChatTitleView = Marionette.ItemView.extend({
        tagName: 'li',
                
        template: '#chat-title-tpl',
        
        className: 'active',
        
        ui: {
            closeBtn: 'i.icon-remove',
            body: 'a'
        },
                
        triggers: {
            'click i.icon-remove': 'removeClicked',
            'click a': 'selected'
        },
                
        onSelected: function() {
            this.$el.closest('ul').find('li.active').removeClass('active');
            this.$el.addClass('active');
        },        
        
        getTitle: function() {
            var result = this.model.getParticipantData(webUser.id).displayName;
            
            if(this.model.get('title'))
                result  += ' - ' + this.model.get('title');
            
            return result; 
        },
        
        serializeData: function() {
            return _.extend(Marionette.ItemView.prototype.serializeData.apply(this, arguments), {
                title: this.getTitle(),
                unviewedCount: ''
            });
        },
                
        onBeforeRender: function() {
            $('div.active-chat-titles-cnt > ul > li').removeClass('active');
        }
    });
    
    var ChatTitlesView = Marionette.CollectionView.extend({
        tagName: 'ul',
        className: 'nav nav-pills convs-tabs-cntr',
        itemView: ChatTitleView
    });

    var ChatView = Marionette.Layout.extend({
        
        template: '#chat-layout-tpl',
        
        regions: {
            messagesReg: 'div.messages-cnt'
        },
        
        ui: {
            feedCount: '.msgs-count'
        },
        
        initialize: function() {
            // Init all related views here
            
            this.messagesView = new MessagesView({
                conversation: this.model,
                collection: this.model.get('messages')
            });
        },
                
        serializeData: function() {
            return _.extend(Marionette.Layout.prototype.serializeData.apply(this), {
               participant: this.model.getParticipantData() 
            });
        },
                
        onRender: function() {
            this.messagesReg.show(this.messagesView);
            this.feedCountView = new FeedCountView({
                el: this.ui.feedCount,
                feed: this.model.get('messages')
            });
        }
    });

    this.openedConversation = null;

    this.activateChat = function(conversation) {
        Chat.activeConversations.add(conversation);
    };
    
    this.openChat = function(conversation) {
        
        this.openedConversation = conversation;
        
        Chat.titlesView.children.findByModel(conversation).$el.addClass('active');
        
        var chat = new ChatView({
            model: conversation
        });

        Chat.activeReg.show(chat);

        conversation.get('messages').fetch({
            success: function() {
                chat.messagesView.render();
            }
        });        
    };

    this.closeChat = function(conversation) {        
        //closing active chat
        if(_.isEqual(this.openedConversation, conversation)) {
            
            this.activeReg.reset();
            
            //select previous if any
            if(this.activeConversations.length) {
                this.openChat(this.activeConversations.first());
            }else{
                this.trigger('allChatsClosed');
            }
        }
    };

    Chat.addInitializer(function(){
        
        Chat.activeConversations = new Backbone.Collection();
        
        Chat.activeReg = new Marionette.Region({
            el: '.active-chat-cnt'
        });
        
        Chat.titlesReg = new Marionette.Region({
           el: '.active-chat-titles-cnt' 
        });
        
        Chat.titlesView = new ChatTitlesView({
            collection: Chat.activeConversations
        });
        
        Chat.titlesView.on('itemview:selected', function(titleView) {
            Chat.openChat(titleView.model);
        });
        
        Chat.titlesView.on('itemview:removeClicked', function(titleView) {
            Chat.activeConversations.remove(titleView.model);
        });
        
        //Will render and show titles panel for the first time
        Chat.activeConversations.once('add', function() {
            Chat.titlesReg.show(Chat.titlesView);
        });
        
        Chat.activeConversations.on({
            'add': function(conversation) {
                Chat.openChat(conversation);    
            },
            'remove': function(conversation) {
                Chat.closeChat(conversation);
            }
        });
        
    });
    
    Chat.addFinalizer(function() {
        
    });
    
    Chat.on('start', function() {
        
    });
});

