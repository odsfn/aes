/* 
 * Extends Marionette's ItemView to provide ability to specify template source code
 * for View
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

var Aes = Aes || {};

Aes.ItemView = Marionette.ItemView.extend({
    
    tpl: '',
    
    template: function(serialized_model) {
        return _.template(this.getTplStr(), _.extend(serialized_model, {modelAttrs: this.serializeData()}));
    },
            
    getTemplate: function() {
        var template = Marionette.ItemView.prototype.getTemplate.apply(this, arguments);
        
        if(typeof template === 'function')
            template = _.bind(template, this);
        
        return template;
    },
            
    getTplStr: function() {
        return this.tpl;
    },
            
    /**
     * Binds attributes for child ui elements which are defined in "ui" property.
     * Attributes are reading from "uiAttributes" property.
     */
    bindUIElAttributes: function() {
        _.each(this.options.uiAttributes, function(attrs, attrName) {
            var uiEl = this.ui[attrName];
            
            if(!uiEl)
                return;
            
            uiEl.attr(attrs);
        }, this);
    },
          
    render: function() {
        Marionette.ItemView.prototype.render.apply(this, arguments);
        
        if(this.options.uiAttributes)
            this.bindUIElAttributes();
        
        return this;
    },        
            
    initialize: function() {
        if(this.options.el)
        {
            this.bindUIElements();
            
            if(this.options.uiAttributes)
                this.bindUIElAttributes();
        }
    }
});
