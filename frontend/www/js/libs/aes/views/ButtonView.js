/* 
 * Button view
 */
var Aes = Aes || {};

Aes.ButtonView = Aes.ItemView.extend({
    
    tagName: 'button',
    
    attributes: {
        class: 'btn'
    },
    
    triggers: {
        'click': 'click'
    },
    
    getTplStr: function() {
        return Aes.ButtonView.getTpl();
    },
    
    onClick: function() {
        var handler = Marionette.getOption(this, 'onClick');
        
        if(handler)
            handler.call(this);
    },
    
    serializeData: function() {
        return _.extend(Aes.ItemView.prototype.serializeData.apply(this, arguments),{
            view: {
                attributes: this.attributes,
                cid: this.cid
            }
        });
    },
    
    initialize: function(options) {
        if(!options.label)
            throw new Error('Label option is required');
        
        Aes.ItemView.prototype.initialize.apply(this, arguments);
        
        this.model = new Backbone.Model({
            label: options.label
        });
    }
    
},{
    getTpl: function() {
        return '<%= label %>';
    }
});
