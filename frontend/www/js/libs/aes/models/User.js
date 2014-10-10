/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.User = Backbone.Model.extend({

    parse: function() {
        var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

//        attrs.birth_day = parseInt(attrs.birth_day) * 1000;
        attrs.user_id = parseInt(attrs.user_id);

        return attrs;
    },

    toJSON: function(options) {
        var json = Backbone.Model.prototype.toJSON.call(this);

        return json;
    }
});