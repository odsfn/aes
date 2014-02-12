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
    }
    
});
