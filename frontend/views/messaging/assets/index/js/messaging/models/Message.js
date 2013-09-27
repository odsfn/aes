/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Message = Backbone.Model.extend({
    defaults: {
        conversation_id: null,
        user_id: null,
        created_ts: null,
        text: null,
        views: []   //{ user_id : null, viewed_ts: null}
    },
    
    urlRoot: UrlManager.createUrlCallback('api/message'),
    
    wasViewed: function(userId) {
        if(this.get('user_id') === userId)
            return true;
        
        if(_.findWhere(this.get('views'), {user_id: userId}))
            return true;
        
        return false;
    }
});

