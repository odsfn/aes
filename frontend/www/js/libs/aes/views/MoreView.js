/*
 * More button for feeds
 *  
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var MoreView = Marionette.ItemView.extend({
    
    template: '#more-btn-tpl',
    
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
        }, this));
        
        this.listenTo(this.base, 'show', _.bind(function() {
            
            var type = typeof this.appendTo;
            
            if(type === 'string')
                $(this.appendTo).append(this.$el);
            else if(type === 'function')
                this.appendTo().append(this.$el);
            else
                this.appendTo.append(this.$el);
            
        }, this));
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
});