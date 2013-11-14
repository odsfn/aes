var Post = Backbone.Model.extend({
    defaults: {
        reply_to: null,
        user_id: null,
        target_id: null,
        user: {
            user_id: null,
            photo: '',
            displayName: '',
        },
        content: '',
        likes: null,
        dislikes: null,
        displayTime: null,
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
            this.rates.post_id = this.id;
        }
        
        this.on('change:id', _.bind(function(){
            this.rates.post_id = this.id;
        }, this));
    },
            
});
