/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
Aes.FilterableCollection = Backbone.Collection.extend({
    
    filters: null,
            
    /**
     * Override this method to provide default set of filters to a collection instance
     */
    getFilters: function() {
       return {};
    },
            
    /**
     * Sets the filter value and fetches first page. All loaded results will be 
     * throwed out, navigation will be reset to the begining
     */
    setFilter: function(name, value) {
        
        this.filters[name] = value;
        
        this.reset();
        this.fetch();
    },
    
    setFilters: function(filters) {
        
        _.extend(this.filters, filters);
        
        this.reset();
        this.fetch();
    },
            
    resetFilters: function(filters) {
        this.filters = filters;
        
        this.reset();
        this.fetch();
    },
            
    fetch: function(options) {
        var options = options || {};
        
        _.extend(options, { 
            data: {
                filter: _.extend({}, this.filters, this.getFilters())
            }
        });
        
        return Backbone.Collection.prototype.fetch.apply(this, arguments);
    },
            
    initialize: function() {
        this.filters = {};
    }
});

