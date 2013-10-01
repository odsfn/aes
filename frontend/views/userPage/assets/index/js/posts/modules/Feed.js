/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Feed', function(Feed, PostsApp, Backbone, Marionette, $, _) {
    
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
            model: new Post({
                targetId: PostsApp.pageUserId
            })
        });

        this.listenTo(this.addPostView, 'edited', this.addPost);
    };
    
    PostsApp.on('start', function() {
        Feed.titleView = new PostsTitleView();
        PostsApp.feedTitleRegion.show(Feed.titleView);
        Feed.resetNewPostRegion();
        
        Feed.listenTo(Feed.posts, 'request', function() {
            $('#posts-app-container').mask();
        });
        
        Feed.listenTo(Feed.posts, 'sync remove add', _.bind(function(collection) {
            $('#posts-app-container').unmask();
        }, this));
        
        Feed.listenTo(Feed.posts, 'sync', function() {
            PostsApp.postsRegion.show(PostsApp.Feed.postsView);
        });
        
        Feed.listenTo(Feed.posts, 'totalCountChanged', _.bind(function(actualValue) {
            Feed.titleView.setRecordsCount(actualValue);
        }, this));
        
        Feed.posts.fetch();
    });

});

