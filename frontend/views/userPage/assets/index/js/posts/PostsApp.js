var PostsApp = new Backbone.Marionette.Application();

PostsApp.addRegions({
    feedTitleRegion: '#feed-title',
    newPostRegion: '#add-post-top',
    postsRegion: '#posts-feed'
});

PostsApp.module('Feed', function(Feed, PostsApp, Backbone, Marionette, $, _) {
    
    console.log('In Feed body');
    /**
     * Posts collection displaying on the user's page
     * @type Posts
     */
    this.posts = new Posts();
    
    this.postsView = new PostsView({
        collection: this.posts
    });
    
    this.resetNewPostRegion = function() {
        this.initAddPostView();
        PostsApp.newPostRegion.show(this.addPostView);        
    };
    
    this.addPost = function(post) {
        this.posts.create(post, {
            success: _.bind(function() {
               this.resetNewPostRegion();
            }, this),
            wait: true
        });
    };
    
    this.initAddPostView = function() {
        this.addPostView = new EditBoxView({
            model: new Post()
        });

        this.listenTo(this.addPostView, 'edited', this.addPost);
    };
    
    PostsApp.on('start', function() {
        console.log('In Feed on start');

        Feed.titleView = new PostsTitleView();
        
        Feed.listenTo(Feed.posts, 'sync', _.bind(function(collection) {
            Feed.titleView.setRecordsCount(collection.totalCount);
        }, this));
        
        Feed.posts.fetch({
            success: function(collection , response, options) {
                PostsApp.feedTitleRegion.show(Feed.titleView);
                PostsApp.postsRegion.show(Feed.postsView);
                Feed.resetNewPostRegion();
            }
        });
    });

});

PostsApp.on('initialize:after', function() {
  Backbone.history.start();
});