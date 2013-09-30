/** 
 * Chat module. 
 * 
 * Controls chat views
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Messaging.Chat', function(Chat, App, Backbone, Marionette, $, _) {
    
    var config = {
        messagesLimit: 50
    };
    
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
        },
                
        appendHtml: function(collectionView, itemView, index){
            collectionView.$el.prepend(itemView.el);
        }                
    });
    
    var ConversationTitleView;
    
    var InputView;
    
    var LoadMsgsBtnView = MoreView.extend({
        template: '#load-msg-btn-tpl',
        ui:{
            body: 'button'
        }
    });
    
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

    /**
     * @TODO Move common functionality of feed view into the FeedView class
     */
    var ChatView = Marionette.Layout.extend({
        
        template: '#chat-layout-tpl',
        
        regions: {
            messagesReg: 'div.messages-cnt'
        },
        
        ui: {
            feedCount: '.msgs-count',
            loadBtn: 'li.load-btn-cnt'
        },
        
        initialize: function() {
            this.messagesView = new MessagesView({
                conversation: this.model,
                collection: this.model.get('messages')
            });
        },
                
        serializeData: function() {
            return _.extend(Marionette.Layout.prototype.serializeData.apply(this), {
               participant: this.model.getParticipantData(webUser.id) 
            });
        },
        
        onRender: function() {
            this.moreView = new LoadMsgsBtnView({
                appendTo: this.ui.loadBtn,
                view: this.messagesView
            });           
        },

        onShow: function() {
    
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
        
        //hide las conversation if any
        if(this.openedConversation) {
            this.activeChatViews.findByModel(this.openedConversation).$el.hide();
        }
        
        this.openedConversation = conversation;
        
        var chat = this.activeChatViews.findByModel(conversation);
        
        var chatTitleView = Chat.titlesView.children.findByModel(conversation);
        
        if(chatTitleView) {
            chatTitleView.$el.addClass('active');
        }
        
        //ChatView not found in active, so should create it
        if(!chat) {

            chat = new ChatView({
                model: conversation
            });

            this.activeChatViews.add(chat);

            $('.active-chat-cnt').append(chat.render().el);
            
            chat.triggerMethod('show');
            
            var messages = conversation.get('messages');

            messages.limit = config.messagesLimit;
            messages.fetch({
                success: function() {
                    chat.messagesView.render();
                }
            });            

        }else{
            
            chat.$el.show();
            
        }
               
    };

    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };

    this.closeChat = function(conversation) {        
        //closing chat
        var activeChatView = this.activeChatViews.findByModel(conversation);
        activeChatView.close();
        this.activeChatViews.remove(activeChatView);
        
        //switch to other opened if active was closed
        if(_.isEqual(this.openedConversation, conversation)) {
            
            this.openedConversation = null;
            
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
        
        Chat.activeChatViews = new Backbone.ChildViewContainer();
        
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

