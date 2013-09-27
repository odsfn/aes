/* 
 * Displays total count of items in related feed. When feed is loading adds loader
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var FeedCountView = Marionette.View.extend({
                
    ui: {
        count: 'span',
        loader: 'img.loader'
    },

    /**
     * @param {FeedCollection} feed Related feed
     */
    feed: null,

    updateCount: function(currentCount, lastCount) {
        this.ui.count.text(currentCount);
    },

    startLoader: function() {
        this.ui.loader.show();
    },


    stopLoader: function() {
        this.ui.loader.hide();
    },

    setFeed: function(feed) {
        feed = feed || this.feed;

        if(feed) {

            if(this.feed && !_.isEqual(feed, this.feed)) {
                this.stopListening(this.feed);
            }

            this.feed = feed;

            this.ui.count.text(this.feed.length);
            this.listenTo(this.feed, 'totalCountChanged', this.updateCount);
            this.listenTo(this.feed, 'request', this.startLoader);
            this.listenTo(this.feed, 'sync', this.stopLoader);
        }
    },

    initialize: function(options) {

        this.bindUIElements();

        var defaults = {
            feed: null
        };

        _.extend(this, _.pick(options, _.keys(defaults)));

        this.setFeed();

        this.startLoader();
    }
});

