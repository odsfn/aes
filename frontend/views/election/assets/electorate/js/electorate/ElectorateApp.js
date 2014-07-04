/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var ElectorateApp = new Marionette.Application();
var App = ElectorateApp;

App.Layout = Marionette.Layout.extend({
    template: '#collect-layout-tpl',
    regions: {
        destination: '#dest-tab',
        source: '#source-tab',
        requested: '#requested-tab'
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

ElectorateApp.NotConfirmedElectorItemView = Aes.UserItemView.extend({
    getControls: function(){
       return {
            remove: {
                text: 'Allow',
                iconType: 'ok',
               
                onBeforeShow: function() {
                    return ElectorateApp.getOption('canInvite') || false;
                },
                       
                onRun: function() {                   
                    this._parent.$el.mask();
                    
                    this._parent.model.set('status', Elector.getStatusId('Active'));
                    this._parent.model.save({}, {
                        success: _.bind(function(model) {
                            this._parent.model.set('added', true);
                            ElectorateApp.electorate.add([model]);
                            ElectorateApp.notConfirmedElectors.remove([model]);
                            this._parent.$el.unmask();
                        }, this),
                        wait: true
                    });                 
                }
            }
        };
    }    
});

ElectorateApp.ElectorateFeedView = ElectorateApp.FeedView.extend({
    itemView: ElectorateApp.ElectorItemView
});

var Electorate = FeedCollection.extend({
    url: UrlManager.createUrlCallback('api/elector'),
    model: Elector,
    setElectionId: function(electionId) {
       this.electionId = electionId;
       this.filters.election_id = electionId;
    },
    getFilters: function() {
        return {
            status: Elector.getStatusId('Active')
        };
    }
});

var NotConfirmedElectorate = Electorate.extend({
    getFilters: function() {
        return {
            status: Elector.getStatusId('NotConfirmed')
        };
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
    
    if(options.showConfirmationTab) {
        this.notConfirmedElectors = new NotConfirmedElectorate();
        this.notConfirmedElectors.setElectionId(options.electionId);
        this.notConfirmedElectorsView = new ElectorateApp.ElectorateFeedView({
            itemView: ElectorateApp.NotConfirmedElectorItemView,
            collection: this.notConfirmedElectors,
            filters: filter
        });
    }
    
    $('body').on('elector_registered', function(e, elector) {
        if(elector.checkStatus('Active'))
            ElectorateApp.electorate.add([elector]);
        else if(elector.checkStatus('NotConfirmed') && options.showConfirmationTab)
            ElectorateApp.notConfirmedElectors.add([elector]);
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
    
    if(options.showConfirmationTab) {
        this.layout.requested.show(this.notConfirmedElectorsView);
        $('#requested-tab-sel').show();
    }
    
    this.electorate.fetch().then(function(){
        if(options.canInvite)
            ElectorateApp.users.fetch();
        
        if(options.showConfirmationTab) {
            ElectorateApp.notConfirmedElectors.fetch();
        }
    });
});