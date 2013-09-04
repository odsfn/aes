var Post = Backbone.Model.extend({
    defaults: {
        reply: null,
        author: {
            id: null,
            photo: '',
            displayName: '',
        },
        content: '',
        likes: 0,
        dislikes: 0,
        displayTime: '',
        comments: []
    },
    
    urlRoot: UrlManager.createUrlCallback('api/post'),
    
    initialize: function() {
        var ratesModels = this.get('rates');
        
        this.rates = new PostRates();
        
        if(ratesModels) {
            this.rates.reset(ratesModels);
        }
        
        if(this.id) {
            this.rates.postId = this.id;
        }
        
        this.on('change:id', _.bind(function(){
            this.rates.postId = this.id;
        }, this));
    },
            
});
