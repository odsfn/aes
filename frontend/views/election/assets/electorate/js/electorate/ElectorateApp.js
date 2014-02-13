/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var ElectorateApp = new Marionette.Application();
var App = ElectorateApp;

App.Layout = Marionette.Layout.extend({
    template: '#collect-layout-tpl',
    regions: {
        destination: '#dest-tab',
        source: '#source-tab'
    }
});

ElectorateApp.UserItemView = Aes.UserItemView.extend({
    
    modelEvents: {
        'change:added': 'render'
    },
    
    getControls: function() {
        return {
            invite: {
                text: 'Add',
                iconType: 'plus-sign',

                onRun: function() {
                    this._parent.$el.mask();
                    ElectorateApp.electorate.add(this._parent.model, {
                        success: _.bind(function() {
                            this._parent.model.set('added', true);
                            this._parent.$el.unmask();
                        }, this),
                        wait: true
                    });
                },

                onBeforeShow: function() {
                    return !this._parent.model.get('added');
                },
                        
                onAfterRun: function() {
                    this._parent.model.set('added', true);
                    this._parent.$el.unmask();                    
                }
            }
        };
    }
});

ElectorateApp.FeedView = Aes.FeedView.extend({
    template: '#source-list-tpl',
    itemView: ElectorateApp.UserItemView 
});

ElectorateApp.ElectorItemView = Aes.UserItemView.extend({
   getControls: function(){
       return {
           remove: {
               text: 'Remove',
               iconType: 'minus-sign',
               
               onBeforeShow: function() {
                    return ElectorateApp.getOption('canInvite') || false;
               },
                       
               onRun: function() {
                   var model = ElectorateApp.users.findWhere({user_id: this._parent.model.get('user_id')});
                   model.set('added', false);
                   
                   ElectorateApp.electorate.remove(this._parent.model);
               }
           }
       };
   }    
});

ElectorateApp.ElectorateFeedView = ElectorateApp.FeedView.extend({
    itemView: ElectorateApp.ElectorItemView
});

App.addInitializer(function(options) {
   
    this.getOption = function(optName) {
        return options[optName];
    };
   
    this.layout = new App.Layout();
    this.users = new Aes.Users();
    this.usersView = new ElectorateApp.FeedView({
       collection: this.users,
       filters: {
        enabled: true
       }
    });
    
    this.electorate = new FeedCollection([]);
    this.electorateView = new ElectorateApp.ElectorateFeedView({
        collection: this.electorate,
        filters: {
            enabled: true
        }
    });
});

App.on('start', function(options) {
    $('#column-right').prepend(this.layout.render().el);
    this.layout.triggerMethod('show');
    
    this.layout.destination.show(this.electorateView);
    
    if(options.canInvite)
    {
        this.layout.source.show(this.usersView);
        this.users.fetch();
        $('#source-tab-sel').show();
    }
});