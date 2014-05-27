var PetitionsCollection = FeedCollection.extend({
    limit: 30,
    model: Petition,
    url: UrlManager.createUrlCallback('api/petition')
});

