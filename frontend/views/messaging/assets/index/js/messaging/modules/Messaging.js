/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Messaging', function(Messaging, App, Backbone, Marionette, $, _) {

    Messaging.addInitializer(function() {
        Messaging.conversations = new Conversations();
    });

    Messaging.addFinalizer(function() {
        Messaging.conversations = null;
        // More tear down
    });

    Messaging.on('start', function() {
        Messaging.conversations.fetch();
    });

});

