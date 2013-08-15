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
    urlRoot: 'api/posts'
});
