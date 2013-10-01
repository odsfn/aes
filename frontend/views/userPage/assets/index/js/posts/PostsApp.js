var App = new Backbone.Marionette.Application();
var PostsApp = App;

App.addRegions({
    feedTitleRegion: '#feed-title',
    newPostRegion: '#add-post-top',
    postsRegion: '#posts-feed'
});

PostsApp.on('initialize:after', function() {
  Backbone.history.start();
});