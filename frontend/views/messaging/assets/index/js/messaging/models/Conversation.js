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
        if(this.messages.length) {
            
            var msgAttrs = this.messages.last().attributes;
            
            return _.extend({}, msgAttrs, {user: this.getUserData(msgAttrs.user_id)});

        } else
            return false;
    },
    
    getParticipant: function(participantId) {
        return _.findWhere(this.get('participants'), {user_id: participantId.toString()}) || null;
    },
    
    getParticipantData: function(userId) {
        return _.filter(this.get('participants'), function(participant) {
           return participant.user_id != userId; 
        })[0].user;
    },
            
    getUserData: function(userId) {
        return _.findWhere(this.get('participants'), {user_id: userId.toString()}).user;
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
    },
            
    hasUnviewedMessages: function(participantId) {
        return !!this.getUnviewedMessages(participantId).length;
    },
    
    getUnviewedMessagesCount: function(participantId) {
        return this.getUnviewedMessages(participantId).length;
    },        
    
    getUnviewedMessages: function(participantId) {
        var 
            participant, 
            lastViewTs,
            unviewedMessages; 
        
        participant = this.getParticipant(participantId);
        
        if(!participant)
            throw new Error('Specified participant id was not found in the conversation participants');
        
        lastViewTs = participant.last_view_ts || 0;
        
        unviewedMessages = _.filter(this.messages.models, function(msg) {
            return (msg.get('user_id') != participantId && msg.get('created_ts') >= lastViewTs);
        });
              
        return unviewedMessages;
    },
            
    updateLastViewTs: function(participantId, ts) {
        ts = ts || (new Date).getTime();
        
        var participant = this.getParticipant(participantId);
        
        participant.last_view_ts = ts;
        
        this.trigger('change:participant:last_view_ts', participant, this);
    }
    
});
