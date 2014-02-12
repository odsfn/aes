/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.User = Backbone.Model.extend({

    parse: function() {
        var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

        attrs.birth_day = parseInt(attrs.birth_day) * 1000;
        attrs.user_id = parseInt(attrs.user_id);

        return attrs;
    },

    toJSON: function(options) {
        var json = Backbone.Model.prototype.toJSON.call(this);

        if(options && _.has(options, 'success')) {
            json.birth_day = this.get('birth_day').toString().substr(0, 10);
        }

        return json;
    }
});