/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
//Custom validators
if(Backbone.Validation) {
    _.extend(Backbone.Validation.validators, {
      greaterThan: function(value, attr, customValue, model) {
        
        var comparingValue = model.get(customValue.attr) || 0;
        
        var value = parseFloat(value);
        
        if(customValue.validOnEqual && value >= comparingValue)
            return;
        
        if(!customValue.validOnEqual && value > comparingValue)
            return;
          
        return 'This field should be greater';
      }
    });
}