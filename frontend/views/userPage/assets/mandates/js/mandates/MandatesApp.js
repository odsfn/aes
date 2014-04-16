/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var App = new Backbone.Marionette.Application(),
    MandatesApp = App;

App.on('start', function() {
    
    var mod = App.module('Mandates');
    
    $('#mandates').prepend(mod.layout.render().el);
    mod.layout.triggerMethod('show');
    
});