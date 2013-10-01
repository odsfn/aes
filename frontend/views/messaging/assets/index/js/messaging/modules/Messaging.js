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
        
        modelEvents: {
            'change:messages': function() {
                this.render();
            }
        },
        
        serializeData: function() {
            return _.extend(Marionette.ItemView.prototype.serializeData.apply(this), {
                initiator: this.model.getInitiatorData(),
                lastMessage: this.model.getLastMessageData(),
                hasUnviewedIncome: this.model.get('messages').hasUnviewed(webUser.id)
            });
        }
    });
    
    var ConversationsView = Marionette.CollectionView.extend({
        itemView: ConversationView
    });

    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };

    Messaging.addInitializer(function() {
        
        Messaging.layout = new MessagingLayout();
        
        Messaging.layout.on('render', function() {
            
            Messaging.convsCountView = new FeedCountView({
                el: $('#convs-count', Messaging.layout.el),
                feed: Messaging.conversations
            });
            
        });
        
        Messaging.conversations = new Conversations();
        
        Messaging.conversations.limit = config.convsLimit;
        
        Messaging.conversationsView = new ConversationsView({
            collection: Messaging.conversations
        });

        this.conversationsView.on('itemview:clicked', function(convView) {
            $('a[href="#active-conv-tab"]').tab('show');
            
            App.module('Messaging.Chat').activateChat(convView.model);
        });

        this.convsMoreBtn = new MoreView({
            view: Messaging.conversationsView,
            appendTo: '#convs-load-btn'
        });
        
        App.module('Messaging.Chat').on('allChatsClosed', function() {
            $('a[href="#conversations-tab"]').tab('show');
        });
    });

    Messaging.addFinalizer(function() {
        Messaging.conversations = null;
        // More tear down
    });

    Messaging.on('start', function() {
        console.log('Messaging.onStart');
        Messaging.conversations.fetch({
            success: function() {
                Messaging.layout.conversations.show(Messaging.conversationsView);
            }
        });
    });

});

