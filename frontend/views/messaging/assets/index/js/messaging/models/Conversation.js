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

        var messages = this.get('messages') || [];
        
        var collection = new Messages(messages, {conversationId: this.get('id')});
        
        this.set('messages', collection);
        
        this.get('messages').on('add', function() {
            this.trigger('change:messages');
        }, this);
    },
            
    getInitiatorData: function() {
        return _.findWhere(this.get('participants'), {user_id: this.get('initiator_id')}).user;
    },
            
    getLastMessageData: function() {
        return this.get('messages').first().attributes;
    },
            
    getParticipantData: function(userId) {
        return _.filter(this.get('participants'), function(participant) {
           return participant.user_id !== userId; 
        })[0].user;
    },
            
    getUserData: function(userId) {
        return _.findWhere(this.get('participants'), {user_id: userId}).user;
    }
    
});
