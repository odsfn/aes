/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Conversation = Backbone.Model.extend({
    defaults: {
        title: null,
        created_ts: null,   /*timestamp*/
        messages: null,
        participants: null, /* MANY:{
            conversation_id: int,
            user_id: int,
            user: {
                user_id: int,
                photo: '',
                displayName: '',
            },
            last_view_ts: timestamp
        } */
        initiator: null     /* user_id */
    },
    
    urlRoot: UrlManager.createUrlCallback('api/conversation'),
    
    initialize: function() {
//        this.set('title', i18n.t('Conversation from {date} (default theme)', {date: i18n.date()}));
        var messages = this.get('messages') || [];
        
        this.set('messages', new Messages(messages));
    }
    
});
