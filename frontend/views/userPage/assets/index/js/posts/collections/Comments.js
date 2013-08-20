/* 
 * Collection for posts that are displayed as comments
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Comments = Backbone.Collection.extend({
   model: Post,
   url: 'api/posts'
});

