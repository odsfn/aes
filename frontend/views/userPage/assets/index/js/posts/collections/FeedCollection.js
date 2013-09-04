/* 
 * This collection represents feed. It encapsulates functionality of 
 * navigation through infinite models feed - like posts. 
 * 
 * @TODO: Resolve how to process models that were added when user is watching
 * his feed
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
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
    
    filters: {},
    
    comparator: function(model) {
        return -model.get('createdTs');
    },
    
    parse: function(response) {

        var 
            fetchedModels = Backbone.Collection.prototype.parse.apply(this, arguments);
        
        this.currentPatchCount = fetchedModels.length;
        
        this.totalCount = response.data.totalCount;
        
        return fetchedModels;
    },
            
    /**
     * Loads next part of data
     */
    fetchNext: function(options) {
        this.offset += this.currentPatchCount;
        _.extend(options, {remove: false});
        this.fetch(options);
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
            
    fetch: function(options) {
        var options = options || {};
        
        if(!this.sinceTs) {
            this.sinceTs = this.getTimestamp();
        }
        
        _.extend(options, { data: _.pick(this, 'offset', 'sinceTs', 'limit', 'filters')});
        
        Backbone.Collection.prototype.fetch.apply(this, [options]);
    },
            
    getTimestamp: function(date) {
        var date = date || new Date();
        return Math.round(date.getTime() / 1000);
    }
});