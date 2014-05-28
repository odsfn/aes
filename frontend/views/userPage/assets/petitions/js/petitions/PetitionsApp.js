/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var App = new Backbone.Marionette.Application(),
    PetitionsApp = App;

App.module('UserRelatedPetitions', function(UserRelatedPetitions, App, Backbone, Marionette, $, _) {
    
    this.startWithParent = false;
    
    var getCommonFiltersConfig = function() {
        return {
            enabled: true,
            type: 'inTopPanel',
            
            submitBtnText: 'Filter',

            fields: {
                title: {
                    label: 'Petition title',
                    type: 'text',

                    filterOptions: {
                        extendedFormat: true
                    }
                }
            }    
        }
    };    
    
    UserRelatedPetitions.PetitionsFeedView = PetitionsFeedView.extend({
        getFiltersConfig: getCommonFiltersConfig
    });
    
    this._initMyPetitions = function(user_id) {
        this.myPetitions = new Petitions([], {
            filters: {
                creator_id: user_id
            }
        });

        this.myPetitionsView = new UserRelatedPetitions.PetitionsFeedView({
            collection: this.myPetitions,
            itemViewOptions: {
                personType: 'mandate'
            }
        });
    };
    
    this._initPetitionsForMe = function(users_mandate_ids) {
        this.petitionsForMe = new Petitions([], {
            filters: {
                mandate_id: {property: 'mandate_id', operator: 'in', value: users_mandate_ids}
            }
        });
        
        this.petitionsForMeView = new UserRelatedPetitions.PetitionsFeedView({
            collection: this.petitionsForMe          
        });
    };
    
    this.addInitializer(function(options) {
        this._initMyPetitions(options.user_id);
        
        if (options.mandates && options.mandates.length > 0) {
            this._initPetitionsForMe(options.mandates);
            this.mainView = new Aes.TabsView({
                tabs: {
                    petitionsForMe: {
                        title: 'Petitions from electors',
                        content: this.petitionsForMeView
                    },
                    myPetitions: {
                        title: 'My petitions for deputies',
                        content: this.myPetitionsView
                    }
                }
            });
            
            this.petitionsForMe.fetch();
        } else {
            this.mainView = this.myPetitionsView;
        }

    });
    
    this.on('start', function() {
        this.myPetitions.fetch().done(function() {
            UserRelatedPetitions.trigger('ready');
        });
    });
});

App.on('start', function(options) {
    
    var mod = App.module('UserRelatedPetitions');
    mod.start(options);
    
    $('#petitions').prepend(mod.mainView.render().el);
    mod.mainView.triggerMethod('show');

});