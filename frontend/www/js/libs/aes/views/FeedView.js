/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.FeedView = Marionette.CompositeView.extend({

    template: function(serialized_model) {
        return _.template(Aes.FeedView.getTpl(), serialized_model);
    },
    
    itemViewContainer: 'div.items',
    emptyView: Aes.NoItemView,
    itemView: Aes.TableItemView,
    moreView: MoreView,
    
    ui: {
        itemsCounter: 'span.items-count',
        loader: 'img.loader'
    },

    appendHtml: function(collectionView, itemView, index){
        var childrenContainer = collectionView.itemViewContainer ? collectionView.$(collectionView.itemViewContainer) : collectionView.$el;
        var children = childrenContainer.children();
        if (children.size() <= index) {
          childrenContainer.append(itemView.el);
        } else {
          children.eq(index).before(itemView.el);
        }
    },
            
    getFiltersConfig: function() {
        var handler = this.options.getFiltersConfig;
        
        if(handler && typeof handler === 'function')
            return handler.apply(this, arguments);
        else
            return false;
    },
            
    initFilters: function(options) {
        
        options = _.clone(options);
        
        delete options.enabled;
        
        var appendTo = '.filter-container';
        
        if(options.type == 'inTopPanel')
            appendTo = '.top-filter-container';
        
        delete options.appendTo;
        
        var that = this;
        
        var filterOptions = {};
        
        for(var filterName in options.fields) {
            var fOpts = options.fields[filterName].filterOptions || false;
            
            if(fOpts && fOpts.extendedFormat){
                filterOptions[filterName] = fOpts;
                delete options.fields[filterName].filterOptions;
            }
        }
        
        var formatFilters = function(filterValues, options) {
            
            for(var filterName in options) {
                var config = options[filterName];
                var filterValue = filterValues[filterName];
                
                if(filterValue == '') {
                    continue;
                }
                
                if(config.value && typeof config.value == 'string' && config.value.search('{value}') != -1)
                    filterValue = config.value.replace('{value}', filterValue);
                else if(config.value && typeof config.value == 'function')
                    filterValue = config.value(filterValue);
                        
                filterValues[filterName] = {property: filterName, value: filterValue};
                
                if(config.operator)
                    filterValues[filterName].operator = config.operator;
            }
            
            return filterValues;
        };
        
        var applyFilters = function() {
            var filters = formatFilters(this.getValues(), filterOptions)
            that.collection.setFilters(filters);
        };
        
        _.extend(options, {
            
           onSubmit: function() {
               applyFilters.apply(this, arguments);
//                that.collection.setFilters(this.getValues());
           },

           onReset: function() {
               applyFilters.apply(this, arguments);
//                that.collection.setFilters(this.getValues());
           }
           
        });
        
        if(options.type == 'inTopPanel')
            this._filter = new Aes.NavbarFormView(options);
        else    
            this._filter = new Aes.FormView(options);
        
        this.on('render', function() {
            this._filter.render();
        });
        
        this.on('show', function() {
            $(appendTo, this.$el).append(this._filter.$el);
            this._filter.trigger('show');
        });
    },

    initialize: function(options) {

        if(options.filters && options.filters.enabled) {
            this.initFilters(options.filters);
        }else if(this.getFiltersConfig() && this.getFiltersConfig().enabled) {
            this.initFilters(this.getFiltersConfig());
        }

        this.model = new Backbone.Model();
        
        this.listenTo(this.collection, 'totalCountChanged', _.bind(function(actualValue) {
            this.model.set('totalCount', actualValue);
        }, this));
        
        this.moreBtnView = new this.moreView({
            view: this,
            appendTo: _.bind(function() { return $('div.load-btn-cntr', this.$el);}, this)
        });        
    },
            
    onShow: function() {
        
        this.ui.itemsCounter.html(this.model.get('totalCount'));
        
        this.listenTo(this.model, 'change:totalCount', function() {
            this.ui.itemsCounter.html(this.model.get('totalCount'));
        }, this);

        this.listenTo(this.collection, 'request', function() {
            this.$el.mask();
            this.ui.loader.show();
        });

        this.listenTo(this.collection, 'sync remove add', _.bind(function(collection) {
            this.$el.unmask();
            this.ui.loader.hide();
        }, this));
    }
}, {
    getTpl: function() {
        return '<div class="navbar head">' 
            + '<div class="navbar-inner">'
                + '<div class="top-filter-container"></div>'
                + '<ul class="nav pull-right">'
                    + '<li><a id="items-count"><img class="loader" src="/img/loader-circle-16.gif" style="display: none;">Found <span class="items-count">0</span> </a></li>'
                + '</ul>'
            + '</div>'
        + '</div>'        

        + '<div class="filter-container"></div>'

        + '<div class="items"></div>'

        + '<div id="load-btn" class="load-btn-cntr"></div>';
    }
});