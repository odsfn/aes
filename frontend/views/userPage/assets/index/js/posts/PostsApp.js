var PostsApp = new Backbone.Marionette.Application();

PostsApp.addRegions({
    newPostRegion: '#add-post-top'
});

PostsApp.module('Feed', function(Feed, PostsApp, Backbone, Marionette, $, _) {
    
    console.log('In Feed body');
    
    this.fetchedPosts = new Backbone.Collection({
        model: Post
    });
    
    PostsApp.on('start', function() {
        console.log('In Feed on start');
        
        Feed.addPostView = new EditBoxView({
            model: new Post()
        });

        Feed.titleView = new PostsTitleView({
            el: $('#title')
        });
        
        PostsApp.newPostRegion.show(Feed.addPostView);
        
        var posts = new Posts();
        posts.fetch({
            success: function(collection , response, options) {
                console.log(JSON.stringify(collection));
            }
        });
    });

});

PostsApp.on('initialize:after', function() {
  Backbone.history.start();
});