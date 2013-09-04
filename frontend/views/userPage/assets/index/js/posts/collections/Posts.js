var Posts = FeedCollection.extend({
    model: Post,
    url: UrlManager.createUrlCallback('api/post'),
});

