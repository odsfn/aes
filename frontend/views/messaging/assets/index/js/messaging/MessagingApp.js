var App = new Backbone.Marionette.Application();

App.on('initialize:before', function() {
    App.module('Messaging.UnviewedIndicator').setOptions({
       messagingModule: App.module('Messaging'),
       chatModule: App.module('Messaging.Chat')
    });
});

App.on('start', function() {
    console.log('App.onStart');
    $('#column-right').prepend(App.module('Messaging').layout.render().el);
    App.module('Messaging').layout.triggerMethod('show');
    
    console.log('Initializing history');
    Backbone.history.start({
        pushState: true,
        root: UrlManager.createUrl('messaging/index')
    });
});