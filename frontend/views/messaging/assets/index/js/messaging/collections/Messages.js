/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Messages = FeedCollection.extend({
    model: Message,
    url: UrlManager.createUrlCallback('api/message'),
    
    comparator: function(model) {
        return model.get('created_ts');
    },
});

