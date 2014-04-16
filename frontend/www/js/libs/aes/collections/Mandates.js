/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var MandatesCollection = FeedCollection.extend({
    limit: 30,
    model: Mandate,
    url: UrlManager.createUrlCallback('api/mandate')
});