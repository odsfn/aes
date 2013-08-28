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
     * The models root in the response
     * 
     * @type String
     */
    root: 'models',
    
    /**
     * Attribute of total count value in the response
     */
    totalCountAttr: 'totalCount',
    
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
    
    comparator: function(model) {
        return -model.get('createdTs');
    },
    
    parse: function(response) {
        var fetchedModels = response[this.root];
        
        this.currentPatchCount = fetchedModels.length;
        
        this.updateNavigation(response, fetchedModels);
        
        return fetchedModels;
    },

    fetchNext: function(options) {
        this.offset += this.currentPatchCount;
        _.extend(options, {remove: false});
        this.fetch(options);
    },        
            
    updateNavigation: function(response, fetchedModels) {
        this.totalCount = response[this.totalCountAttr];
    },
            
    fetch: function(options) {
        if(!this.sinceTs) {
            this.sinceTs = this.getTimestamp();
        }
        
        _.extend(options, { data: _.pick(this, 'offset', 'sinceTs', 'limit')});
        
        Backbone.Collection.prototype.fetch.apply(this, [options]);
    },
            
    getTimestamp: function(date) {
        var date = date || new Date();
        return Math.round(date.getTime() / 1000);
    }
});