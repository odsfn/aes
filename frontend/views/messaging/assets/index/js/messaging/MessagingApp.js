var App = new Backbone.Marionette.Application();

App.on('initialize:after', function() {
  Backbone.history.start();
});

App.on('start', function() {
    console.log('App.onStart');
    $('#column-right').prepend(App.module('Messaging').layout.render().el);
});