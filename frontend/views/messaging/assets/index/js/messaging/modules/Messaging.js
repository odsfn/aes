/** 
 * Messaging module. Shows system messages, lists conversations and allows to 
 * chat with others.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Messaging', function(Messaging, App, Backbone, Marionette, $, _) {

    var config = {
        convsLimit: 20
    };

    var MessagingLayout = Marionette.Layout.extend({
        
        template: '#messaging-layout',
        
        regions: {
            conversations: '#convs-container',
            
            convsLoader: '#convs-load-btn'
        }
    });
    
    var ConversationView = Marionette.ItemView.extend({
        template: '#conversation',
        
        triggers: {
            'click': 'clicked'
        },

        initialize: function() {
            this.listenTo(this.model.messages, 'add', this.render);
        },
                
        serializeData: function() {
            return _.extend(Marionette.ItemView.prototype.serializeData.apply(this), {
                participant: this.model.getParticipantData(webUser.id),
                lastMessage: this.model.getLastMessageData(),
                hasUnviewedIncome: this.model.messages.hasUnviewed(webUser.id)
            });
        }
    });
    
    var ConversationsView = Marionette.CollectionView.extend({
        itemView: ConversationView
    });

    Messaging.Router = Marionette.AppRouter.extend({

        appRoutes: {
            "chat_with/:userId": "startChat"
        }
        
    });

    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };

    this.startChat = function(userId) {
          this.switchToTab('actives');
          
          var existingConv = new Conversation();
          
          existingConv.fetch({
              data: {
                  filter: {
                    initiator_id: webUser.id,
                    participant_id: userId
                  }
              },
              success: function(model, response) {
                  
                    if(response.data.models.length === 1) {     //existing conversation found
                        
                        Messaging.conversations.add(existingConv, {merge: true});
                        existingConv = Messaging.conversations.get(existingConv);   //get merged model, it has listeners
                    
                        App.module('Messaging.Chat').activateChat(existingConv);
                        
                    } else {                                    //not found, so create one

                        existingConv.set({

                            participants: [         
                                {
                                    user_id: webUser.id,
                                },
                                {
                                    user_id: 155,                     
                                }
                            ],

                            initiator_id: webUser.id
                        });
                        
                        existingConv.save(null, {
                            wait: true,
                            success: function() {
                                existingConv.messages.once('add', function() {
                                    Messaging.conversations.add(existingConv, {merge: true});
                                }, this);
                                
                                App.module('Messaging.Chat').activateChat(existingConv);                                
                            }
                        });
                    }
              }
          });
    };

    /**
     * Opens specified tab, triggers event
     * @param {string} tabName  The logical name of tab where we are switching
     */
    this.switchToTab = function(tabName) {
        var tabNameToIdMap = {
            actives: 'active-conv-tab',
            conversations: 'conversations-tab'
        };
        
        $('a[href="#' + tabNameToIdMap[tabName] + '"]').tab('show');
        this.triggerMethod('tabOpened:' + tabName, tabName);
    };

    Messaging.addInitializer(function() {
        var chatModule = App.module('Messaging.Chat');
        
        this.on('tabOpened:conversations', function() {
            this.router.navigate('');
        });
        
        this.on('tabOpened:actives', function() {
            var openedConv = App.module('Messaging.Chat').openedConversation || false;
            
            if(openedConv) {
                this.router.navigate('chat_with/' + openedConv.getParticipantData(webUser.id).user_id);
            }else
                this.router.navigate('chats');
        });
        
        this.listenTo(chatModule, 'chat:opened', function(chatView) {
            this.router.navigate('chat_with/' + chatView.model.getParticipantData(webUser.id).user_id);
        }, this);
        
        Messaging.layout = new MessagingLayout();
        
        Messaging.layout.on({
            render: function() {
                Messaging.convsCountView = new FeedCountView({
                    el: $('#convs-count', Messaging.layout.el),
                    feed: Messaging.conversations
                });
            },
            show: function() {
                $('.nav-tabs a').click(function(e) {
                    e.preventDefault();
                    
                    var tabName = 'conversations',
                        href = $(this).attr('href');
                    
                    if(href === '#conversations-tab') {
                        tabName = 'conversations';
                    }else if(href === '#active-conv-tab'){
                        tabName = 'actives';
                    }
                    
                    Messaging.switchToTab(tabName);
                });
            }
        });
        
        Messaging.conversations = new Conversations();
        
        Messaging.conversations.limit = config.convsLimit;
        
        Messaging.conversationsView = new ConversationsView({
            collection: Messaging.conversations
        });

        this.conversationsView.on('itemview:clicked', function(convView) {
            this.switchToTab('actives');
            
            App.module('Messaging.Chat').activateChat(convView.model);
        }, this);

        this.convsMoreBtn = new MoreView({
            view: Messaging.conversationsView,
            appendTo: '#convs-load-btn'
        });
        
        chatModule.on('allChatsClosed', function() {
            this.switchToTab('conversations');
        }, this);
        
        this.router = new Messaging.Router({
            controller: this
        });
    });

    Messaging.addFinalizer(function() {
        Messaging.conversations = null;
        // More tear down
    });

    Messaging.on('start', function() {
        Messaging.conversations.fetch({
            success: function() {
                Messaging.layout.conversations.show(Messaging.conversationsView);
            },
            merge: true,
            remove: false
        });
    });

});

