var App = new Backbone.Marionette.Application();

App.on('initialize:after', function() {
  Backbone.history.start();
});