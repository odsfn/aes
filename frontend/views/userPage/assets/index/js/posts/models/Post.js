var Post = Backbone.Model.extend({
    defaults: {
        reply: null,
        author: null,
        content: '',
        likes: 0,
        dislikes: 0,
        comments: []
    }
});
