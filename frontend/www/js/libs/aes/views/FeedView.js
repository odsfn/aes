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

    onFilterApplyBtnClick: function(e) {
        e.preventDefault();

        var filters = {};

        $('.filter', this.$el).each(function(i, el) {
            var $el = $(el);
            var filterName = $el.attr('name');
            var filterVal = $el.val() || '';
            
            filters[filterName] = filterVal;
        });

        this.collection.setFilters(filters);
    },

    onFilterResetBtnClick: function(e) {
        e.preventDefault();

        var filters = {};

        $('.filter', this.$el).each(function(i, el) {
            var $el = $(el);
            var filterName = $el.attr('name');
            $el.val('');
            
            filters[filterName] = '';
        });
        
        this.collection.setFilters(filters);
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

    initFilters: function(o) {
        var defaults = {
            filterApplyClassName: 'filter-apply',
            filterResetClassName: 'filter-reset'
        };
        
        o = _.extend(defaults, o);
        
        var events = {};
        events['click .' + o.filterApplyClassName] = 'onFilterApplyBtnClick';
        events['click .' + o.filterResetClassName] = 'onFilterResetBtnClick';
        
        this.events = this.events || {};
        
        _.extend(this.events, events);
    },

    initialize: function(options) {

        if(options.filters && options.filters.enabled) {
            this.initFilters(options.filters);
        }

        this.listenTo(this.collection, 'totalCountChanged', _.bind(function(actualValue) {
            this.ui.itemsCounter.html(actualValue);
        }, this));

        this.listenTo(this.collection, 'request', function() {
            this.$el.mask();
            this.ui.loader.show();
        });

        this.listenTo(this.collection, 'sync remove add', _.bind(function(collection) {
            this.$el.unmask();
            this.ui.loader.hide();
        }, this));

        this.moreBtnView = new this.moreView({
            view: this,
            appendTo: _.bind(function() { return $('div.load-btn-cntr', this.$el);}, this)
        });
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

        + '<div class="items"></div>'

        + '<div id="load-btn" class="load-btn-cntr"></div>';
    }
});