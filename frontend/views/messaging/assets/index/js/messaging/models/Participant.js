/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Participant = Backbone.Model.extend({
    defaults: {
        conversation_id: null,
        user_id: null,
        last_view_ts: null
    },
    
    urlRoot: UrlManager.createUrlCallback('api/conversationParticipant'),
    
    parse: function() {
        var attrs = Backbone.Model.prototype.parse.apply(this, arguments);
        
        attrs.last_view_ts = parseInt(attrs.last_view_ts) * 1000;
        attrs.conversation_id = parseInt(attrs.conversation_id);
        attrs.user_id = parseInt(attrs.user_id);
        attrs.id = parseInt(attrs.id);
        
        return attrs;
    },
            
    toJSON: function() {
        var json = Backbone.Model.prototype.toJSON.call(this);
        json.last_view_ts = this.get('last_view_ts').toString().substr(0, 10);
        return json;
    }
});

