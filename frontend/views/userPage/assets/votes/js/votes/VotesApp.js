/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var App = new Backbone.Marionette.Application(),
    VotesApp = App;

App.on('start', function() {
    
    var modVotes = App.module('UsersVotes');
    
    $('#votes').prepend(modVotes.layout.render().el);
    modVotes.layout.triggerMethod('show');
    
});