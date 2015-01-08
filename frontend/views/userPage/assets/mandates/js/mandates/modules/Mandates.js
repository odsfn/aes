/**
 * Lists users mandates
 *   
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Mandates', function(Mandates, App, Backbone, Marionette, $, _) {
    
    var config = {
        userId: null,
        canDeleteComments: false
    };
    
    var MandateView = Aes.ItemView.extend({
        template: '#mandate-tpl',
                
        ui: {
            rates: '.rates-container',
            comments: '.comments-container'
        },                 
                
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
            
            if(!this._rates)
            {
                this._rates = RatesWidget.create({
                    targetId: this.model.get('id'),
                    targetType: 'Mandate',
                    autoFetch: false,
                    initData: {
                        positiveRatesCount: this.model.get('positiveRatesCount'),
                        negativeRatesCount: this.model.get('negativeRatesCount'),
                        models: this.model.get('rates')
                    }
                });
            }
            
            this.ui.rates.prepend(this._rates.render().$el);
            this._rates.delegateEvents();
            
            if(!this._comments)
            {
                this._comments = CommentsWidget.create({
                    targetId: this.model.get('id'),
                    targetType: 'Mandate'
                });
            }
            
            this.ui.comments.prepend(this._comments.render().$el);
            this._comments.delegateEvents();
        },
          
        onShow: function() {
            this._rates.trigger('show');
            this._comments.trigger('show');
        }
    });
    
    var MandatesFeedView = Aes.FeedView.extend({
        itemView: MandateView,
        
        getFiltersConfig: function() {
            return {
                type: 'inTopPanel',
                
                enabled: true,

                submitBtnText: 'Filter',
                
                uiAttributes: {
                    form: {
                        class: 'navbar-search form-inline span4'
                    }
                },

                fields: {
                    name: {
                        label: 'Type mandate name',
                        type: 'text',
                        
                        filterOptions: {
                            extendedFormat: true
                        },
                        
                        uiAttributes: {
                            input: {
                                class: 'span6'
                            }
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
        this.mandates.filters.user_id = config.userId;
        
        this.mandatesFeedView = new MandatesFeedView({
            collection: this.mandates
        });
        
        this.layout = new Layout({
           template: '#mandates-layout'
        });
        
//        if(config.canDeleteComments)
//        {
//            WebUser.addRoles('commentsAdmin');
//        }
    });
    
    this.on('start', function() {
        
        this.mandates.fetch().done(function(){
           Mandates.layout.mandates.show(Mandates.mandatesFeedView); 
        });
        
    });
    
});