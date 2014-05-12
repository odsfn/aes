/**
 * Lists petitions for specified mandate 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('PetitionsList', function(PetitionsList, App, Backbone, Marionette, $, _) {
    
    // prevent starting with parent
    this.startWithParent = false;    
    
    var config = {
        mandateId: null,
        petitionsCanBeRated: false,
        layoutTpl: '#petitions-list-layout-tpl'
    };
    
    var Petition = Backbone.Model.extend({
        
        parse: function(attrs) {
            
            attrs.created_ts = parseInt(attrs.created_ts) * 1000;
            
            return attrs;
        }
    });
    
    var Supporter = Backbone.Model.extend({
        parse: function(attrs) {
            
            attrs.profile.birth_day = parseInt(attrs.profile.birth_day) * 1000;
            
            return attrs;
        }
    });
    
    var PetitionsCollection = FeedCollection.extend({
        limit: 30,
        model: Petition,
        url: UrlManager.createUrlCallback('api/petition')
    });
    
    PetitionsList.PetitionView = Aes.ItemView.extend({
        /*
         * Whether to shorten content
         */
        shortContent: true,
        
        personType: 'creator',
        
        template: '#petition-tpl',      
        
        ui: {
            rates: '.petition-rates'
        },
        
        onRender: function() {
            this.ui.rates.prepend(this._rates.render().$el);
            this._rates.delegateEvents();
        },
                
        onShow: function() {
            this._rates.trigger('show');
        },
                
        serializeData: function() {
            
            var person = this.model.get('mandate').candidate.profile;
            var personType = Marionette.getOption(this, 'personType');
            
            if(personType === 'creator') {
                person = this.model.get('creator');
            }
    
            var shortContent = false;
            
            if (Marionette.getOption(this, 'shortContent')) {
                shortContent = this.getShortContent();
            }
    
            return _.extend(Aes.ItemView.prototype.serializeData.apply(this, arguments), {
               shortContent: shortContent,
               personType: personType,
               person: person
            });
        },
                
        getShortContent: function() {
            var text = this.model.get('content');
            var length = 512;
            
            if(text.length > length) {
                text = text.substr(0, length) + '...';
            }
            
            return text;
        },
        
        getRates: function() {
            return this._rates;
        },
        
        _initializeRates: function() {
            this._rates = RatesWidget.create({    
                rateViewTemplate: '#petition-rates-tpl',

                targetId: this.model.get('id'),
                targetType: 'Petition',

                canRateChecker: function() {
                    return PetitionsList.getPetitionsCanBeRated();
                }
            });
        },
        
        initialize: function() {
            Aes.ItemView.prototype.initialize.apply(this, arguments);
            
            this._initializeRates();
        }
    });
    
    PetitionsList.PetitionDetailedView = PetitionsList.PetitionView.extend({
        template: '#petition-detailed-tpl',
        shortContent: false
    });
    
    var PetitionsFeedView = Aes.FeedView.extend({
        template: '#petitions-feed-tpl',
        itemView: PetitionsList.PetitionView,
        
        getFiltersConfig: function() {
            return {
                
                enabled: true,

                submitBtnText: 'Filter',

                uiAttributes: {
                    form: {
                        class: 'span3 well'
                    },
                    inputs: {
                        class: 'span12'
                    }
                },

                fields: {
                    title: {
                        label: 'Petition title',
                        type: 'text',
                        
                        filterOptions: {
                            extendedFormat: true
                        }
                    },
                    creator_name: {
                        label: 'Authored by'
                    },
                    support: {
                        label: 'Support type',
                        type: 'radio-group',
                        options: [
                            {label: 'Any', value: 'any', checked: true},
                            {label: 'Created by me', value: 'created_by_user'},
                            {label: 'Supported by me', value: 'supported_by_user'}
                        ]
                    },
                    creation_date: {
                        label: 'Creation date',
                        type: 'radio-group',
                        options: [
                            {label: 'Any', value: 'any', checked: true},
                            {label: 'Today', value: 'today'},
                            {label: 'This week', value: 'week'},
                            {label: 'This month', value: 'month'}
                        ]
                    }
                }  
            };
        }
    });
    
    var SupporterView = Aes.ItemView.extend({
        className: 'user-info',
        template: '#electorfeed-item-tpl'
    });
    
    var Layout = Marionette.Layout.extend({
        regions: {
           petitions: '#petitions-feed-container',
           petitionDetails: '#petition-details'
        },
       
        render: function() {
            if (!this._wasRendered) {
                Marionette.Layout.prototype.render.apply(this, arguments);
                this._wasRendered = true;
            } else {
                this.regionManager.each(function(region) {
                    if(!region.currentView) return;
                    
                    region.currentView.render();
                    region.currentView.delegateEvents();
                });
            }
            
            return this;
        },       
    });
    
    PetitionsList.DetailsLayout = Marionette.Layout.extend({
        template: '#petition-details-layout-tpl',
        
        regions: {
            petitionInfo: '#petition-info',
            tabs: '#petition-tabs'
        },
        
        render: function() {
            if (!this._wasRendered) {
                Marionette.Layout.prototype.render.apply(this, arguments);
                this._wasRendered = true;
            }
            
            return this;
        },
        
        onShow: function() {
            this.options.onShow.call(this);
        },
        
        initialize: function() {
            this.render();
        }
    });
    
    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };
    
    this.getPetitionsCanBeRated = function() {
        return config.petitionsCanBeRated;
    };
    
    this.initPetitionDetails = function(petition, callback) {
        
        var petitionView = new PetitionsList.PetitionDetailedView({
            shortContent: false,
            model: petition
        });
        
        /**
         * @todo Move initialization of supportersFeedView to the RatesWidget or RatesView
         */
        var supportersFeed = new FeedCollection([], {
            model: Supporter,
            filters: {
                target_id: petition.get('id'),
                with_profile: true
            }
        });
        supportersFeed.url = UrlManager.createUrlCallback('api/Petition_rate');
        
        supportersFeed.listenTo(petitionView.getRates().getRatesCollection(), 'add', function(rate, collection) {
            this.listenToOnce(collection, 'sync', function() {
                this.fetch();
            });
        });
        
        supportersFeed.listenTo(petitionView.getRates().getRatesCollection(), 'remove', function(rate, collection) {
            var rate = this.get(rate);
            if (rate) this.remove(rate);
        });
        
        var supportersFeedView = new Aes.FeedView({
            itemView: SupporterView,
            collection: supportersFeed,
            filters: {
                type: 'inTopPanel',
                
                enabled: true,

                fields: {
                    name: {
                        label: 'Name',
                        type: 'text'
                    }
                }
            }
        });
        supportersFeed.fetch();
        
        var petitionTabsView = new Aes.TabsView({
            tabs: {
                supporters: {
                    title: 'Supporters',
                    content: supportersFeedView
                },
                discussion: {
                    title: 'Discussion',
                    content: CommentsWidget.create({
                        emptyView: true,
                        targetId: petition.get('id'),
                        targetType: 'Petition'
                    })
                }
            }
        });        
        
        var details = new PetitionsList.DetailsLayout({
            petitionView: petitionView,
            petitionTabsView: petitionTabsView,
            onShow: function() {
                this.petitionInfo.show(this.options.petitionView);
                this.tabs.show(this.options.petitionTabsView);
            }
        });
        
        callback(details);
    };
    
    this.addInitializer(function(options) {
        
        this.setOptions(options);
        
        this.petitions = new PetitionsCollection();
        this.petitions.filter.mandate_id = config.mandateId;
        
        this.petitionsFeedView = new PetitionsFeedView({
            collection: this.petitions
        });
        
        this.layout = new Layout({
           template: config.layoutTpl
        });
        
    });
    
    this.on('start', function() {
        
        $.when(
            this.petitions.fetch()
        ).done(function(){
            PetitionsList.layout.petitions.show(PetitionsList.petitionsFeedView);
            PetitionsList.trigger('ready');
        });
        
    });
    
});

