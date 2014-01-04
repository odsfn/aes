/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var FeedWidget = {};

FeedWidget.FeedItemView = Marionette.ItemView.extend({

    className: 'feed-item',

    template: '#feed-item-tpl'

});

FeedWidget.NoItemView = Marionette.ItemView.extend({
    template: '#feed-no-item-tpl'
});

FeedWidget.FeedView = Marionette.CollectionView.extend({
        
    itemView: FeedWidget.FeedItemView,

    emptyView: FeedWidget.NoItemView

});

