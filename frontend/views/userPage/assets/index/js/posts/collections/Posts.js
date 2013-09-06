var Posts = FeedCollection.extend({
    model: Post,
    url: UrlManager.createUrlCallback('api/post'),
    getFilters: function() {
        return {
            userPageId: PostsApp.pageUserId
        };
    }
});

