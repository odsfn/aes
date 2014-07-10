/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var App = new Backbone.Marionette.Application(),
    AdminsApp = App;

App.Router = Marionette.AppRouter.extend({
    appRoutes: {
        "": 'viewCandidates',
        "details/:candId": 'viewDetails'
    }
});

App.on('start', function() {
    console.log('CandidatesApp.start');
    var modCands = App.module('Candidates');
    
    this.router = new App.Router({
        controller: modCands
    });
    
    $('#candidates').prepend(modCands.layout.render().el);
    modCands.layout.triggerMethod('show');
    
    $('body').on('candidate_registered', function(e, candidate) {
        modCands.cands.add([candidate]);
    });
    
    $('#column-right, #title').on('click', 'a.route', function(e) {
        e.preventDefault();
        App.router.navigate($(this).attr('href'), {trigger: true});
    });
});