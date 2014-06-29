/* 
 * This collection represents feed. It encapsulates functionality of 
 * navigation through infinite models feed - like posts.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 * @todo Extend it from Aes.FilterableCollection. Note: you should rename "filter" property call
 * to "filters" in every places
 */
var FeedCollection = Backbone.Collection.extend({

    /**
     * Offset value for the next fetch
     */
    offset: 0,
    
    /**
     * How many models to get per fetch
     */
    limit: 20,
    
    /**
     * The moment when feed was firstly loaded 
     */
    sinceTs: null,
    
    /**
     * This attribute contains total count of models since feed was firstly loaded
     */
    totalCount: 0,
    
    /**
     * Count of models which has been currently fetched
     */
    currentPatchCount: 0,
    
    filters: null,
    
    _requestProcessing: false,
    
    /**
     * To provide default filters you can override this method.
     * You can pass filters property with constructors' options also, or getFilters method
     * 
     * @returns Set of default filters which will be applied on every fetch
     */
    getFilters: function() {
        var filters = {};
        
        if (this.options && this.options.filters) {
            filters = this.options.filters;
        } else if (this.options && this.options.getFilters && _.isFunction(this.options.getFilters)) {
            filters = this.options.getFilters.apply(this);
        }
        
        return filters;
    },
    
    comparator: function(model) {
        return -model.get('created_ts');
    },
    
    parse: function(response, options) {

        var 
            fetchedModels = Backbone.Collection.prototype.parse.apply(this, arguments);
        
        this.currentPatchCount = fetchedModels.length;
        
        if(_.has(response, 'data') && _.has(response.data, 'totalCount'))
            this.setTotalCount(parseInt(response.data.totalCount));
        else if(_.has(options, 'totalCount'))
            this.setTotalCount(parseInt(options.totalCount));
        
        return fetchedModels;
    },
            
    /**
     * Loads next part of data
     */
    fetchNext: function(options) {
        this.offset = this.models.length;
        _.extend(options, {remove: false});
        return this.fetch(options);
    },        
    
    /**
     * Sets the filter value and fetches first page. All loaded results will be 
     * throwed out, navigation will be reset to the begining
     */
    setFilter: function(name, value) {
        
        this.filters[name] = value;
                
        this.sinceTs = null;
        this.offset = 0;
        
        this.reset();
        this.fetch();
    },
    
    setFilters: function(filters) {
        
        _.extend(this.filters, filters);

        this.sinceTs = null;
        this.offset = 0;
        
        this.reset();
        this.fetch();
    },
            
    resetFilters: function(filters) {
        this.filters = filters;

        this.sinceTs = null;
        this.offset = 0;
        
        this.reset();
        this.fetch();
    },
            
    fetch: function(options) {
        var options = options || {},
            params = _.pick(this, 'offset', 'sinceTs', 'limit');
        
        if(!this.sinceTs) {
            this.sinceTs = this.getTimestamp();
        }
        
        _.extend(params, {
            filter: _.extend({}, this.filters, this.getFilters())
        });
        
        _.extend(options, { data: params});
        
        return Backbone.Collection.prototype.fetch.apply(this, [options]);
    },
            
    reset: function(models, options) {

        this.offset = 0;
        this.totalCount = 0;
        this.currentPatchCount = 0;
        
        Backbone.Collection.prototype.reset.apply(this, arguments);
    },
            
    getTimestamp: function(date) {
        var date = date || new Date();
        return Math.round(date.getTime() / 1000);
    },
    
    incrementCount: function() {
        this.setTotalCount(this.totalCount+1);
    },
            
    decrementCount: function() {
        this.setTotalCount(this.totalCount-1);
    },
            
    setTotalCount: function(value) {
        var lastValue = this.totalCount;
        this.totalCount = parseInt(value);
        this.trigger('totalCountChanged', value, lastValue);
    },
    
    initialize: function(models, options) {

        if (options) {
            this.options = options;
        }

        var restoreIncrCount = _.bind(function() {
            // @TODO: move this "_.findWhere(this._events['add'], {callback: this.incrementCount})" to the method Backbobe.Events.hasHandler(eventName, callback, context) 
            if(!_.findWhere(this._events['add'], {callback: this.incrementCount}))
                this.on('add', this.incrementCount);
            
        }, this);

        this.filters = {};

        this.on('request', function() {
           this._requestProccessing = true;
           
           //totalCount will be returned by server
           this.off('add', this.incrementCount);
        });

        this.on('sync', function() {
           restoreIncrCount();
           
           this.sort();
           
           this._requestProccessing = false;
        });
        
        this.on('remove', function() {
            this.decrementCount();
            
            if(this._requestProccessing === true)
                restoreIncrCount();
            
            this._requestProccessing = false;
        });
        
    }
});