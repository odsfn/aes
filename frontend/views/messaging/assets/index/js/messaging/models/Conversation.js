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
        initiator_id: null     /* user_id */
    },
    
    urlRoot: UrlManager.createUrlCallback('api/conversation'),
    
    initialize: function() {              
        this.initMessages();
        
        this.on('change:id', function() {
            this.initMessages();
        }, this);
    },
            
    getInitiatorData: function() {
        return _.findWhere(this.get('participants'), {user_id: this.get('initiator_id')}).user;
    },
            
    getLastMessageData: function() {
        if(this.messages.length)
            return this.messages.first().attributes;
        else
            return false;
    },
            
    getParticipantData: function(userId) {
        return _.filter(this.get('participants'), function(participant) {
           return participant.user_id != userId; 
        })[0].user;
    },
            
    getUserData: function(userId) {
        return _.findWhere(this.get('participants'), {user_id: userId}).user;
    },
            
    initMessages: function() {
        
        var id = this.get('id');
        
        if(id === null) 
            return;
        
        var messages = this.get('messages') || [];
        
        this.messages = new Messages(messages, {conversationId: id});
        
        this.on('change:messages', function() {
            this.messages.add(this.get('messages'), {merge: true});
        }, this);
    }
    
});
