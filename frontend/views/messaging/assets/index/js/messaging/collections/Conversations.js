/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Conversations = FeedCollection.extend({
    model: Conversation,
    
    url: UrlManager.createUrlCallback('api/conversation'),
    
    comparator: function(model) {
        return -model.get('created_ts');
    },
});