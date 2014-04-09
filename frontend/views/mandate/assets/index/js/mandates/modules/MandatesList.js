/**
 * Lists mandates, and it's details
 *   
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('MandatesList', function(MandatesList, App, Backbone, Marionette, $, _) {
    
    var config = {
        layoutTpl: '#mandates-list-layout'
    };
    
    var Mandate = Backbone.Model.extend({
        parse: function(attrs) {
            
            attrs.submiting_ts = parseInt(attrs.submiting_ts) * 1000;
            attrs.expiration_ts = parseInt(attrs.expiration_ts) * 1000;
            
            return attrs;
        },
                
        getStatusText: function() {
            return Mandate.getStatuses()[this.get('status')];
        },

        checkStatus: function(statusText) {
            var statuses = Mandate.getStatuses();

            if(_.indexOf(statuses, statusText) === -1)
                throw new Error('Status "' + statusText + '" does not exist');

            return (statuses[this.get('status')] === statusText);
        }    
    }, {
        getStatuses: function() {
            return ['Active', 'Expired', 'Revoked'];
        }
    });
    
    var MandatesCollection = FeedCollection.extend({
        limit: 30,
        model: Mandate,
        url: UrlManager.createUrlCallback('api/mandate')
    });
    
    var MandateView = Aes.ItemView.extend({
        template: '#mandate-tpl',
//        
//        ui: {
//        },
//        
//        events: {
//            'click .body': 'onBodyClick'
//        },
                
        getStatusClass: function() {
            var status = this.model.getStatusText();
            
            return 'status-' + status.toLowerCase();
        },
        
        serializeData: function() {
            return _.extend(
                   Aes.ItemView.prototype.serializeData.apply(this, arguments),
                   {
                       statusText: this.model.getStatusText()
                   }
            );
        },      
                
        onRender: function() {
            this.$el.removeClass();
            this.$el.addClass(this.getStatusClass());
        }
    });
    
    var MandatesFeedView = Aes.FeedView.extend({
        template: '#mandates-feed-tpl',
        itemView: MandateView,
        
        
        
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
                    name: {
                        label: 'Mandate name',
                        type: 'text',
                        
                        filterOptions: {
                            extendedFormat: true
                        }
                    },
                    owner_name: {
                        label: 'Owner name',
                        type: 'text'
                    },
                    status: {
                        label: 'Status',
                        type: 'select',
                        options: [
                            {label: 'Any', value: '', selected: true},
                            {label: 'Active', value: 0},
                            {label: 'Expired', value: 1},
                            {label: 'Revoked', value: 2},
                        ],
                                
                        filterOptions: {
                            extendedFormat: true
                        }
                    }
                }  
            };
        }
    });
    
    var Layout = Marionette.Layout.extend({
       regions: {
           mandates: '#mandates-feed-container'
       } 
    });
    
    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };
    
    this.addInitializer(function(options) {
        
        this.setOptions(options);
        
        this.mandates = new MandatesCollection();
        
        this.mandatesFeedView = new MandatesFeedView({
            collection: this.mandates
        });
        
        this.layout = new Layout({
           template: config.layoutTpl
        });
        
    });
    
    this.on('start', function() {
        
        this.mandates.fetch().done(function(){
           MandatesList.layout.mandates.show(MandatesList.mandatesFeedView); 
        });
        
    });
    
});