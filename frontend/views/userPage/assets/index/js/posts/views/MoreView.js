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
        a: 'a',
        loader: 'span',
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
            console.log('Base view rendered. Adding More button');
            this.render();
            this.delegateEvents();
            
            $(this.appendTo).append(this.$el);
            
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
                if(collection.currentPatchCount == 0)
                    this.$el.hide();
                else
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
    }
});