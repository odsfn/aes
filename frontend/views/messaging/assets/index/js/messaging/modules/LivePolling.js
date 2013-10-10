/**
 * Loads new messages to the chat without need of page refreshing. Implemets it
 * by polling with interval.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Messaging.LivePolling', function(LivePolling, App, Backbone, Marionette, $, _) { 
    
    var ConvsPoller = Backbone.Collection.extend({
        
        lastRequestTime: null,
        
        since: null,
        
        model: Conversation,
        
        url: UrlManager.createUrlCallback('api/conversation'),
        
        initialize: function() {
            this.on('request', function() {
                this.lastRequestTime = (new Date).getTime();
                this.since = this.lastRequestTime;
            });
        },
                
        fetch: function(options) {
    
            options = _.extend({}, options, {
                data: {
                    filter: {
                        since: this.since - 3000
                    }
                }
            });
            
            Backbone.Collection.prototype.fetch.call(this, options);
        }
    });
    
    var conf = {
            poller: {
                delay: 20000,
                delayed: true
            }
        },
        
        conversations, convsPoller;
    
    this.setOptions = function(options) {
        conf = _.defaults(options, conf);
    };
    
    this.addInitializer(function() {
        conversations = new ConvsPoller();
        
        convsPoller = Backbone.Poller.get(conversations, conf.poller);
        
        convsPoller.on('success', function(conversations) {
            
            conversations.each(function(conversation) {
                
                // throw away own messages
                var messages = _.filter(conversation.messages.models, function(message) { 
                    return (message.get('user_id') != webUser.id); 
                });

                if(messages.length > 0)
                    App.module('Messaging').triggerMethod('receiveMessages', messages, conversation);
                    
            });
            
            //flush collection, because messages were proccessed
            conversations.reset();
        });
    });
    
    this.on('start', function() {
        // fetch messages from the module's start
        conversations.since = (new Date).getTime();
        convsPoller.start();
    });
    
});

