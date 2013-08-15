var Posts = Backbone.Collection.extend({
    model: Post,
    url: 'api/posts',
    comparator: function(model) {
        return -model.get('createdTs');
    }
});

