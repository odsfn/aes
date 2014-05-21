/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var App = new Backbone.Marionette.Application(),
    MandatesApp = App;

App.Router = Marionette.AppRouter.extend({
    appRoutes: {
        "": 'viewMandates',
        "details/:mandId/petition_:petitionId(/)": 'viewPetitionDetails',
        "details/:mandId(/*subsection(/))": 'viewDetails'
    }
});

App.on('start', function() {
    var modMands = App.module('MandatesList');

    this.router = new App.Router({
        controller: modMands
    });

    $('#mandates').prepend(modMands.layout.render().el);
    modMands.layout.triggerMethod('show');

    $('#mandates').on('click', 'a.route', function(e) {
        e.preventDefault();
        App.router.navigate($(this).attr('href'), {trigger: true});
    });

    this.listenTo(modMands, 'ready', function() {
        Backbone.history.start({
            pushState: true,
            root: UrlManager.createUrl('mandate/index')
        });
    });
});

