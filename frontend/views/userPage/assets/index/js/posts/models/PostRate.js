/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var PostRate = Backbone.Model.extend({
    
    defaults: {
        score: null,
        postId: null,
        userId: null,
        createdTs: null
    }
    
});

var PostRates = Backbone.Collection.extend({
    
    postId: null,
    
    model: PostRate,
    
    parse: function(response) {
        this.likes = response.likes;
        this.dislikes = response.dislikes;
        return response.rates;
    }, 
            
    initialize: function(options) {
        var options = options || {};
        
        _.defaults(options, {
            postId: null
        });
        
        _.extend(this, _.pick(_.keys(options)));
    },
            
    url: function() {
        return UrlManager.createUrl('api/post/' + this.postId + '/rates');
    },
            
    getLikes: function() {
        return this.where({score: 1}).length;
    },
            
    getDislikes: function() {
        return this.where({score: -1}).length;
    },
    
    addRate: function(userId, score) {
        return this.create({
           postId: this.postId,
           userId: userId,
           score: score
        });
    },        
            
    getRate: function(userId) {
        return this.findWhere({userId: userId});
    }
});