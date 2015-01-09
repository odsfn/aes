/* 
 * This collection represents feed. It encapsulates functionality of 
 * navigation through infinite models feed - like posts.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

var FeedCollection = Aes.FeedCollection = Aes.FilterableCollection.extend({

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
    
    _requestProcessing: false,
    
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
        this.sinceTs = null;
        this.offset = 0;
        
        Aes.FilterableCollection.prototype.setFilter.apply(this, arguments);
    },
    
    setFilters: function(filters) {
        this.sinceTs = null;
        this.offset = 0;
        
        Aes.FilterableCollection.prototype.setFilters.apply(this, arguments);
    },
            
    resetFilters: function(filters) {
        this.sinceTs = null;
        this.offset = 0;
        
        Aes.FilterableCollection.prototype.resetFilters.apply(this, arguments);
    },
            
    fetch: function(options) {
        if(!this.sinceTs) {
            this.sinceTs = this.getTimestamp();
        }
        
        if(options === undefined) {
            var options = {};
            Array.prototype.push.call(arguments, options);
        }
        
        var params = _.pick(this, 'offset', 'sinceTs', 'limit');
        
        if(options.data === undefined)
            options.data = {};
        
        _.extend(options.data, params);
        
        return Aes.FilterableCollection.prototype.fetch.apply(this, arguments);
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
    
    allLoaded: function() {
        return this.totalCount === this.models.length;
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