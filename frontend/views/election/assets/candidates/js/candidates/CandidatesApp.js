/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var App = new Backbone.Marionette.Application(),
    AdminsApp = App;

App.on('initialize:before', function() {

});

App.on('start', function() {
    var modCands = App.module('Candidates');
    
    $('#column-right').prepend(modCands.layout.render().el);
    modCands.layout.triggerMethod('show');
});