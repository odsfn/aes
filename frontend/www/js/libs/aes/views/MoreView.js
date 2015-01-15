/*
 * More button for feeds
 * 
 * @depends Aes.ItemView
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var MoreView = Aes.ItemView.extend({
    
    getTplStr: function() {
        return MoreView.getTpl();
    },
    
    events: {
        'click': 'onClick'
    },
    
    ui: {
        body: 'div.span12'
    },
    
    initialize: function(options) {
        var defaults = {
            appendTo: '',
            moreMsg: 'More',
        };
        
        _.defaults(options, defaults);
        
        _.extend(this, _.pick(options, _.keys(defaults)));
        
        this.base = options.view;
        
        this.model = new Backbone.Model({});
        
        this.listenTo(this.base, 'render', _.bind(function() {
            this.render();
            this.delegateEvents();
            
            if(this.base.collection.allLoaded && this.base.collection.allLoaded()) {
                this.$el.hide();
            } else {
                this.$el.show();
            }
            
            if(this.base._isShown) {
                this._appendToBase();
            }
            
        }, this));
        
        this.listenTo(this.base, 'show', this._appendToBase);
    },
    
    _appendToBase: function() {
        var type = typeof this.appendTo;

        if(type === 'string')
            $(this.appendTo).append(this.$el);
        else if(type === 'function')
            this.appendTo().append(this.$el);
        else
            this.appendTo.append(this.$el);        
    },
    
    serializeData: function() {
        return _.extend(Marionette.ItemView.prototype.serializeData.call(this), {
            view: {
                moreMsg: this.moreMsg
            }
        });
    },
    
    onClick: function() {
        if(this.loading) 
            return;
        
        this.startLoader();
        this.base.collection.fetchNext({
            success: _.bind(function(collection) {
                if(collection.currentPatchCount == 0) {
                    this.$el.hide();
                    this.listenTo(this.base.collection, 'reset', _.bind(function() {
                        this.$el.show();
                    }, this));
                }
                
                this.stopLoader();
                
            }, this)
        });
    },
    
    startLoader: function() {
        this.loading = true;
        this.ui.body.addClass('loading');
    },
            
    stopLoader: function() {
        this.loading = false;
        this.ui.body.removeClass('loading');
        this.trigger('loaded');
    }
}, {
    getTpl: function() {
        return '<div class="get-more"><div class="span12"><a><%= t(view.moreMsg) %></a><span><img src="/img/loader-circle-16.gif" class="loader" />Loading...</span></div></div>';
    }
});