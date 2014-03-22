/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var App = new Backbone.Marionette.Application(),
    NominationsApp = App;

App.on('start', function() {
    
    var modNominations = App.module('Nominations');
    
    $('#nominations').prepend(modNominations.layout.render().el);
    modNominations.layout.triggerMethod('show');
    
});