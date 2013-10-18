/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Conversations = FeedCollection.extend({
    model: Conversation,
    
    url: UrlManager.createUrlCallback('api/conversation'),
    
    comparator: function(m1, m2) {
        
        if(m1.hasUnviewedMessages(webUser.id) && !m2.hasUnviewedMessages(webUser.id))
            return -1;
            
        else if(m2.hasUnviewedMessages(webUser.id) && !m1.hasUnviewedMessages(webUser.id))
            return 1;
            
        if(m1.get('created_ts') > m2.get('created_ts'))
            return -1;
        else if(m1.get('created_ts') === m2.get('created_ts'))
            return 0;
        else
            return 1;
            
    }
});