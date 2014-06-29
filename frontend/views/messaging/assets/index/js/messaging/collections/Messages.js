/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Messages = FeedCollection.extend({
    
    model: Message,
    
    url: UrlManager.createUrlCallback('api/message'),
    
    comparator: function(model) {
          return model.get('created_ts');
    },
            
    fetchUnviewed: function(userId) {
        return this.models.filter(function(message) {
            return !message.wasViewed(userId);
        });
    },
            
    unviewedCount: function(userId) {
        return this.fetchUnviewed(userId).length;
    },
            
    hasUnviewed: function(userId) {
        return !!this.unviewedCount(userId);
    },

    initialize: function(models, conf) {

        FeedCollection.prototype.initialize.apply(this, arguments);

        if(conf.conversationId)
            this.filters = { conversation_id: conf.conversationId };
        
    }
});

