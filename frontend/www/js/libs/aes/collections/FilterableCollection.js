/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.FilterableCollection = Backbone.Collection.extend({
    
    filters: null,
            
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
        
        if(options.data === undefined)
            options.data = {};
        
        _.extend(options.data, {
                filter: _.extend({}, this.filters, this.getFilters())
        });
        
        return Backbone.Collection.prototype.fetch.apply(this, arguments);
    },
            
    initialize: function() {
        this.filters = {};
    }
});

