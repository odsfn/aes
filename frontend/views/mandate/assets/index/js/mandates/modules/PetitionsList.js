/**
 * Lists petitions for specified mandate 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('PetitionsList', function(PetitionsList, App, Backbone, Marionette, $, _) {
    
    // prevent starting with parent
    this.startWithParent = false;    
    
    var config = {
        mandateId: null,
        layoutTpl: '#petitions-list-layout-tpl'
    };
    
    var Petition = Backbone.Model.extend({
        
        parse: function(attrs) {
            
            attrs.created_ts = parseInt(attrs.submiting_ts) * 1000;
            
            return attrs;
        }
    });
    
    var PetitionsCollection = FeedCollection.extend({
        limit: 30,
        model: Petition,
        url: UrlManager.createUrlCallback('api/petition')
    });
    
    var PetitionView = Aes.ItemView.extend({
        
        personType: 'creator',
        
        template: '#petition-tpl',      
        
        ui: {
            rates: '.petition-rates'
        },
        
        onRender: function() {
            if(!this._rates)
            {
                this._rates = RatesWidget.create({
                    targetId: this.model.get('id'),
                    targetType: 'Petition',
                    targetEl: this.ui.rates
                });
            }

            this.ui.rates.prepend(this._rates.render().$el);
            this._rates.delegateEvents();
            this._rates.bindEventsToTarget($('.rates', this.$el));
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
    
            return _.extend(Aes.ItemView.prototype.serializeData.apply(this, arguments), {
               shortContent: this.getShortContent(),
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
        }
    });
    
    var PetitionsFeedView = Aes.FeedView.extend({
//        template: '#petitions-feed-tpl',
        itemView: PetitionView,
        
//        getFiltersConfig: function() {
//            return {
//                
//                enabled: true,
//
//                submitBtnText: 'Filter',
//
//                uiAttributes: {
//                    form: {
//                        class: 'span3 well'
//                    },
//                    inputs: {
//                        class: 'span12'
//                    }
//                },
//
//                fields: {
//                    name: {
//                        label: 'Petition name',
//                        type: 'text',
//                        
//                        filterOptions: {
//                            extendedFormat: true
//                        }
//                    },
//                    owner_name: {
//                        label: 'Owner name',
//                        type: 'text'
//                    },
//                    status: {
//                        label: 'Status',
//                        type: 'select',
//                        options: [
//                            {label: 'Any', value: '', selected: true},
//                            {label: 'Active', value: 0},
//                            {label: 'Expired', value: 1},
//                            {label: 'Revoked', value: 2},
//                        ],
//                                
//                        filterOptions: {
//                            extendedFormat: true
//                        }
//                    }
//                }  
//            };
//        }
    });
    
    var Layout = Marionette.Layout.extend({
       regions: {
           petitions: '#petitions-feed-container',
           petitionDetails: '#petition-details'
       } 
    });
    
    var DetailsLayout = Marionette.Layout.extend({
        template: '#petition-details-layout-tpl',
        regions: {
            petitionInfo: '#petition-info',
            supportersTabContent: '#supporters-tab'
        }
    });
    
    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };
    
//    this.viewPetitions = function() {
//        this.layout.petitionDetails.close();
//        this.layout.petitions.$el.show();
//        $('#petition-details li.node-viewDetails').remove();
//    };
    
//    this.viewDetails = function(petitionId) {
//        var petition = this.petitions.findWhere({id: petitionId});
//        
//        this.layout.petitions.$el.hide();
//        
//        this.layout.petitionDetails.show(this.detailsLayout);
//        this.detailsLayout.petitionInfo.show(new PetitionView({
//            template: '#petition-detailed-tpl',
//            model: petition
//        }));
//    };
    
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
        
//        this.detailsLayout = new DetailsLayout();
    });
    
    this.on('start', function() {
        
        this.petitions.fetch().done(function(){
           PetitionsList.layout.petitions.show(PetitionsList.petitionsFeedView);
           PetitionsList.trigger('ready');
        });
        
    });
    
});

