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
    
    var Supporter = Backbone.Model.extend({
        parse: function(attrs) {
            
            attrs.profile.birth_day = parseInt(attrs.profile.birth_day) * 1000;
            
            return attrs;
        }
    });
    
    PetitionsList.PetitionView = PetitionView.extend({
        canRateChecker:  function() {
            return PetitionsList.getPetitionsCanBeRated();
        }
    });
    
    PetitionsList.PetitionDetailedView = PetitionsList.PetitionView.extend({
        template: '#petition-detailed-tpl',
        shortContent: false
    });
    
    PetitionsList.PetitionsFeedView = PetitionsFeedView.extend({
        itemView: PetitionsList.PetitionView
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
        }       
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
        this.petitions.filters.mandate_id = config.mandateId;
        
        this.petitionsFeedView = new PetitionsList.PetitionsFeedView({
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

