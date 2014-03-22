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

    initFilters: function(options) {
        
        options = _.clone(options);
        
        delete options.enabled;
        
        var appendTo = '.filter-container';
        
        delete options.appendTo;
        
        var that = this;
        
        _.extend(options, {
            
           onSubmit: function() {
               that.collection.setFilters(this.getValues());
           },

           onReset: function() {
               that.collection.setFilters(this.getValues());
           }
           
        });
        
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