/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Rate = Backbone.Model.extend({

    defaults: {
        score: null,
        target_id: null,
        user_id: null,
        createdTs: null
    },

    parse: function(rawData) {

        rawData = _.clone(rawData);

        rawData = Backbone.Model.prototype.parse.apply(this, arguments);

        rawData.created_ts = parseInt(rawData.created_ts) * 1000;

        var toInt = ['id', 'score', 'target_id', 'user_id'];

        for(var i = 0; i < toInt.length; i++) {
            rawData[toInt[i]] = parseInt(rawData[toInt[i]]);
        }

        return rawData;
    }                
});

var Rates = Aes.FilterableCollection.extend({

    target_id: null,

    model: Rate,

    initialize: function(models, options) {
        Aes.FilterableCollection.prototype.initialize.apply(this, arguments);

        var options = options || {};

        this.url = options.url;
        this.target_id = options.target_id;
        this.filters.target_id = this.target_id;
    },

    getLikes: function() {
        return this.where({score: 1}).length;
    },

    getDislikes: function() {
        return this.where({score: -1}).length;
    },

    addRate: function(user_id, score) {
        return this.create({
           target_id: this.target_id,
           user_id: user_id,
           score: score
        });
    },        

    getRate: function(user_id) {
        return this.findWhere({user_id: user_id});
    }
});

var RatesWidget = (function(){
    
    var RatesView = Marionette.ItemView.extend({
        template: '#rates-tpl',
                
        className: 'rates',
        
        ui: {
            ratePlus: 'span.icon-thumbs-up',
            rateMinus: 'span.icon-thumbs-down'
        },

        events: {
            'mouseenter .rate-control': 'onActivate',
            'mouseleave .rate-control': 'onDeactivate',
            'click span.icon-thumbs-up': 'onRatePlus',
            'click span.icon-thumbs-down': 'onRateMinus'
        },
                
        onRatePlus: function() {
            this.rate('up');
        },

        onRateMinus: function() {
            this.rate('down');
        },

        onRender: function() {

            var rate = this.getCurrentUserRate();
            //Mark user's vote
            
            if(rate !== false) {
                this.markRate(rate);
            }
            
        },

        markRate: function(rate) {
            if(rate === 1) {
                this.ui.ratePlus.addClass('chosen');
            }else{
                this.ui.rateMinus.addClass('chosen');
            }
        },

        unmarkRate: function(rate) {
            if (rate === 1) {
                this.ui.ratePlus.removeClass('chosen');
            } else
                this.ui.rateMinus.removeClass('chosen');            
        },

        getRatesCollection: function() {
            return this.ratesCollection;
        },

        getCurrentUserRate: function() {
            var rate;
            
            if(WebUser.isGuest() || !(rate = this.ratesCollection.getRate(WebUser.getId())))
                return false;
            
            return parseInt(rate.get('score'));
        },

        /**
         * Determines whether the RateView allow current user to rate.
         * 
         * This is default implementation. You can override it through extension
         * or by passing canRateChecker attribute ( should be a function ) to the
         * constructor options.
         * 
         * @returns {Boolean}
         */
        canRateChecker: function() {
            return WebUser.hasAccess('RatesView.rate', this);
        },

        canRate: function() {
            return Marionette.getOption(this, 'canRateChecker').apply(this, arguments);
        },

        rate: function(score) {

            if(!this.canRate())
                return;

            if(score === 'up') {
                score = 1;
            }else{
                score = -1;
            }

            var 
                lastRate = this.ratesCollection.getRate(WebUser.getId()),
                lastRateScore = null;

              if(lastRate) {
                  lastRateScore = lastRate.get('score');
                  
                  if(score == lastRateScore) {
                      lastRate.destroy();
                      return;
                  }
                  
                  this.ratesCollection.remove(lastRate);
              }

              this.ratesCollection.addRate(WebUser.getId(), score);

        },

        onActivate: function() {
            if(this.canRate())
                this.$el.addClass('active');
        },

        onDeactivate: function() {
           this.$el.removeClass('active');  
        },

        serializeData: function() {
            var serializedData = Marionette.ItemView.prototype.serializeData.apply(this, arguments);

            _.extend(serializedData, {
                likes: this.ratesCollection.getLikes(),
                dislikes: this.ratesCollection.getDislikes(),
                currentUserRate: this.getCurrentUserRate()
            });

            return serializedData;
        },   
                
        initialize: function(options) {
    
            this.ratesCollection = options.ratesCollection;
            
            this.listenTo(this.ratesCollection, 'add', _.bind(function(rate, collection){
                this.markRate(rate.get('score'));
                this.render();
            }, this));

            this.listenTo(this.ratesCollection, 'remove', _.bind(function(rate, collection){
                this.unmarkRate(rate.get('score'));
                this.render();
            }, this));     
        }
    });
    
    var defaultConfig = {

        targetId: null,
        
        targetType: null,
        
        rateViewTemplate: '#rates-tpl',
        
        canRateChecker: false,
        
        webUser: WebUser || null,
        
        urlManager: UrlManager || null, 
        
        templates: {
            ratesView: '#rates-tpl',
        },

        initData: {
            totalCount: 0,
            models: []
        },
        
        autoFetch: true,
        
        urls: {
            
            rates: null
        },
        
    };
    
    WebUser.addAccessRules({
               
       "RatesView": {
           "rate": function(view) {
               return this.isAuthenticated();
           }
       }
       
    });
    
    var ratesCollections = new (Backbone.Collection.extend({
        getRatesCol: function() {
            
            var targetType = arguments[0];
            var targetId = arguments[1];
            var config = false;
            
            if (_.isObject(arguments[0])) {
                config = arguments[0];
                targetType = config.targetType;
                targetId = config.targetId;
            }
            
            var col = this.findWhere({
                targetType: targetType,
                targetId: targetId
            });
            
            if (!col && config) {
                col = new Backbone.Model({
                    targetType: targetType,
                    targetId: targetId,
                    collection: this._initRatesCollection(config)
                });
                
                this.add([
                    col
                ]);
            }
            
            if (!col) {
                return false;
            }
            
            return col.get('collection');
        },
        
        _initRatesCollection: function(config) {
            var ratesCol = new Rates([], {
                target_id: config.targetId,
                url: config.urls.rates
            });

            if(config.initData.models.length > 0)   //setting up init data rows if any provided
                ratesCol.reset(config.initData.models, {parse: true, totalCount: config.initData.totalCount});

            return ratesCol;
        }        
    }))();
     
    return {
        create: function(options) {
            
            var view, config, urlManager;
            
            config = _.extend({}, defaultConfig, options);
            
            urlManager = config.urlManager;            
            
            if(!config.urls.rates && !config.ratesCollection)
                config.urls.rates = urlManager.createUrl('api/' + config.targetType + '_rate');
            
            
            if(!config.ratesCollection) {
                var ratesCol = ratesCollections.getRatesCol(config);
            } else {
                var ratesCol = config.ratesCollection;
            }
            
            var ratesViewOptions = {
                template: config.rateViewTemplate,
                ratesCollection: ratesCol
            };
            
            if(typeof config.canRateChecker === 'function')            
                ratesViewOptions.canRateChecker = config.canRateChecker;
            
            view = new RatesView(ratesViewOptions);
            
            if(config.autoFetch && config.initData.models.length == 0)            
                view.once('show', function() {
                   view.ratesCollection.fetch({
                       success: function() {
                           view.render();
                       }
                   });
                });          
               
            return view;
        }
    };    
    
})();

