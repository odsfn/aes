/**
 * Displays count of income unviewed messages in the specified existing element
 * on the page. Stores information about last view of conversations.
 * 
 * It will be also responsible for showing pop-ups with income messages 
 *  
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Messaging.UnviewedIndicator', function(UnviewedIndicator, App, Backbone, Marionette, $, _) {
    
    var conf = {
        elToAppend: '#column-left ul.nav li.messaging > a',
        messagingModule: null,
        chatModule: null
    };
    
    /**
     * interface Messaging {
     *  getConversations(): Conversations
     * }
     * 
     * @type {Messaging}
     */
    var messagingModule = null;
    
    /**
     * interface Chat {
     *  getOpenedConversation(): Conversation
     * }
     * @type {Chat}
     */
    var chatModule = null;
    
    var MsgsInCountView = Marionette.ItemView.extend({
       tagName: 'span',
       
       template: '#msgs-in-count-tpl',
       
       className: 'pull-right',
       
       modelEvents: {
           'change:count': 'countChanged'
       },
       
       countChanged: function() {
           this.render();
           this._processVisibility();
       },
               
       onShow: function() {
           this._processVisibility();
       },
               
       _processVisibility: function() {
           if(this.model.get('count') == 0)
               this.$el.hide();
           else
               this.$el.show();       
       }
    });
    
    this.setOptions = function(options) {
        conf = _.defaults(options, conf);
    };
    
    this.setCount = function(count) {
        this.countView.model.set('count', count);
    };
    
    this.addCount = function(count) {
        this.setCount(this.getCount() + count);
    };

    this.subCount = function(count) {
        this.setCount(this.getCount() - count);
    };
    
    this.getCount = function() {
        return this.countView.model.get('count');
    };

    /**
     * Count of unviewed messsages
     * @returns int
     */
    this.getUnviewedCount = function() {
        var count = 0;
        
        messagingModule.conversations.each(function(conv) {
            count += conv.getUnviewedMessagesCount(WebUser.getId());
        });
        
        return count;
    };    
    
    this.addInitializer(function() {
        
       messagingModule = conf.messagingModule;
       chatModule = conf.chatModule;
        
       this.countView = new MsgsInCountView({
           model: new Backbone.Model({count: 0})
       });
       
       this.listenTo(messagingModule, {
           'messagesReceived conversationReceived': function(conv, msgs, inCount) {
               if(!chatModule || !chatModule.getOpenedConversation() || chatModule.getOpenedConversation().get('id') != conv.get('id'))
                   this.addCount(inCount);
               else
                   conv.updateLastViewTs(WebUser.getId());
           },
           'conversationsFetched': function() {
                this.setCount(this.getUnviewedCount());
           }
       });
       
       this.listenToOnce(messagingModule, 'conversationsFetched', function(conversations) {
           this.listenTo(conversations, 'change:participant:last_view_ts', function(participantAttrs, conv) {
                this.setCount(this.getUnviewedCount());

                var participant = new Participant();
                participant.save(participantAttrs);
           }); 
       });
       
       if(chatModule) {
           this.listenTo(chatModule, 'chat:opened', function(chat) {
                chat.model.updateLastViewTs(WebUser.getId());
           });
       }
    });
    
    this.on('start', function() {
        if(conf.elToAppend) {
            $(conf.elToAppend).append(this.countView.render().el);
            this.countView.triggerMethod('show');
        }
    });
});