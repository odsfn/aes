/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var PostRate = Backbone.Model.extend({
    
    defaults: {
        score: null,
        post_id: null,
        user_id: null,
        createdTs: null
    }
    
});

var PostRates = Backbone.Collection.extend({
    
    post_id: null,
    
    model: PostRate,
    
    url: UrlManager.createUrlCallback('api/postRate'),
            
    initialize: function(options) {
        var options = options || {};
        
        _.defaults(options, {
            post_id: null
        });
        
        _.extend(this, _.pick(_.keys(options)));
    },
            
    getLikes: function() {
        return this.where({score: 1}).length;
    },
            
    getDislikes: function() {
        return this.where({score: -1}).length;
    },
    
    addRate: function(user_id, score) {
        return this.create({
           post_id: this.post_id,
           user_id: user_id,
           score: score
        });
    },        
            
    getRate: function(user_id) {
        return this.findWhere({user_id: user_id});
    }
});