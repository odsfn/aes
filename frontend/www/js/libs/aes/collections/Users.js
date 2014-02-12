/**
 * All users collection
 *
 * @depends FeedCollection, Aes.User  
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.Users = FeedCollection.extend({
    limit: 25,
    model: Aes.User,
    url: UrlManager.createUrlCallback('api/profile')
}); 

