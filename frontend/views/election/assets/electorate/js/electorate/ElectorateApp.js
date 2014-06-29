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
                    
                    ElectorateApp.electorate.create( 
                        {
                            election_id: ElectorateApp.getOption('electionId'),
                            user_id: this._parent.model.get('user_id')
                        }, 
                        {
                            success: _.bind(function() {
                                this._parent.model.set('added', true);
                                this._parent.$el.unmask();
                            }, this),
                            wait: true
                        }
                    );
                },

                onBeforeShow: function() {
                    var result = !this._parent.model.get('added');
                    return result;
                }
            }
        };
    }
});

ElectorateApp.FeedView = Aes.FeedView.extend({
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
                   if(model)
                        model.set('added', false);
                   
                   this._parent.model.destroy();
               }
           }
       };
   }    
});

ElectorateApp.ElectorateFeedView = ElectorateApp.FeedView.extend({
    itemView: ElectorateApp.ElectorItemView
});

var Elector = Backbone.Model.extend({
    parse: function() {
        var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

        attrs.id = parseInt(attrs.id);
        attrs.user_id = parseInt(attrs.user_id);
        attrs.election_id = parseInt(attrs.election_id);

        if(attrs.profile)
        {
            var profile = new Aes.User(attrs.profile, {parse: true});
            _.extend(attrs, profile.attributes);
            
            delete attrs.profile;
        }
        
        return attrs;
    },

    toJSON: function(options) {
        var json = Backbone.Model.prototype.toJSON.call(this);

        if(json.profile)
            delete json.profile;

        return json;
    }
});

var Electorate = FeedCollection.extend({
   url: UrlManager.createUrlCallback('api/elector'),
   model: Elector,
   setElectionId: function(electionId) {
       this.electionId = electionId;
       this.filters.election_id = electionId;
   }
});

App.addInitializer(function(options) {
    
    var filter = {
           
        enabled: true,
        
        attributes: {
            class: 'search-form pull-right span4'
        },
        
        uiAttributes: {
           inputs: {
                class: 'span12'
           }
        },
        
        fields: {
                name: {
                    label: 'Name',
                    type: 'text',
                },
                birth_place: {
                    label: 'Birth Place'
                },
                ageFrom: {
                    label: 'Age From',
                    validator: {
                        required: false,
                        min: 1,
                        max: 100
                    }
                },
                ageTo: {
                    label: 'Age To',
                    validator: {
                        required: false,
                        min: 1,
                        max: 100,
                        greaterThan: {
                            attr: 'ageFrom',
                            validOnEqual: true
                        }
                    }
                },
                gender: {
                    label: 'Gender',
                    type: 'select',
                    options: [
                        {label: 'Any', value: '', selected: true},
                        {label: 'Male', value: '1'},
                        {label: 'Female', value: '2'}
                    ]
                }
         }
        
       };
    
    this.getOption = function(optName) {
        return options[optName];
    };
   
    this.layout = new App.Layout();
    this.users = new Aes.Users();
    this.users.filters.applyScopes = '{notElector: {election_id: '+ options.electionId +'}}';
    this.usersView = new ElectorateApp.FeedView({
       collection: this.users,
       filters: filter
    });
    
    this.electorate = new Electorate();
    this.electorate.setElectionId(options.electionId);
    
    this.electorateView = new ElectorateApp.ElectorateFeedView({
        collection: this.electorate,
        filters: filter
    });
});

App.on('start', function(options) {
    $('#column-right').prepend(this.layout.render().el);
    this.layout.triggerMethod('show');
    
    this.layout.destination.show(this.electorateView);
    
    if(options.canInvite) {
        this.layout.source.show(this.usersView);
        $('#source-tab-sel').show();
    }
    
    this.electorate.fetch().then(function(){
        if(options.canInvite)
            ElectorateApp.users.fetch();
    });
});