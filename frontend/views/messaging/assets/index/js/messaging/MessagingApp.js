var App = new Backbone.Marionette.Application();

App.on('start', function() {
    console.log('App.onStart');
    $('#column-right').prepend(App.module('Messaging').layout.render().el);
    App.module('Messaging').layout.trigger('show');
    
    console.log('Initializing history');
    Backbone.history.start({
        pushState: true,
        root: UrlManager.createUrl('messaging/index')
    });
});