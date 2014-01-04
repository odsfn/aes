/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var App = new Backbone.Marionette.Application(),
    AdminsApp = App;

App.on('initialize:before', function() {
    console.log('Admins management app initialize:before');
});

App.on('start', function() {
    var modAdmins = App.module('AdminsManagement');
    
    $('#column-right').prepend(modAdmins.layout.render().el);
    modAdmins.layout.triggerMethod('show');
});