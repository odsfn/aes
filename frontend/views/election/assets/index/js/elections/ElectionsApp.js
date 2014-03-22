var App = new Backbone.Marionette.Application();

App.addRegions({
    form: '#election_form',
    list: '#election_list',
    more: '#election_more'
});

var Elections = FeedCollection.extend({//Backbone.Collection.extend({//FeedCollection.extend({
    model: Election,
    url: UrlManager.createUrlCallback('api/election'),

    limit: 3,

    fetch: function(options) {
        var options = options || {};

        _.extend(options, { success: function(collection) {

            $('#total_elections').text(collection.totalCount);

            if(collection.length < collection.totalCount) {
                App.moreView.$el.show();
            } else {
                App.moreView.$el.hide();
            }

            App.moreView.stopLoader();

        }
        });

        FeedCollection.prototype.fetch.apply(this, [options]);
    }

});


var ElectionView = Marionette.ItemView.extend({
    template: '#election-view'
});

var NoElectionView = Marionette.ItemView.extend({
    template: '#no-election-view'
});

var ElectionsView = Marionette.CollectionView.extend({
    itemView: ElectionView,
    emptyView: NoElectionView
});

var FormView = Marionette.ItemView.extend({
    template: '#search-view',
    events: {
        'keyup #elect_search': 'filterChange',
        'change #elect_status': 'filterChange',
        'click #a_more': 'fetchMore'
    },

    ui: {
        input: '#elect_search',
        select: '#elect_status',
        a_more: '#a_more'
    },

    filterChange: function(init) {
        var param = [];
        if (init !== true) {
            var search_val = $('#elect_search').val(), select_val = $('#elect_status').val();
            if (search_val !== '')
                param[0] = {property: 'name', value : '%'+search_val+'%'};
            if (select_val !== '')
                param[1] = {property: 'status', value : select_val};
        }
        this.collection.reset();
        this.collection.filter = param;
        this.collection.fetch();
    },

    initialize: function(){
        this.filterChange(true);
    }

});

var MoreView = Marionette.ItemView.extend({
    template: '#more-btn-tpl',
    events: {
        'click': 'fetchMore'
    },
    ui: {
        a_more: '#a_more',
        loader: 'span',
        body: 'div.span12'
    },
    fetchMore: function() {
        if(this.loading)
            return;

        this.startLoader();

        this.collection.fetchNext({});
    },

    startLoader: function() {
        this.loading = true;
        this.ui.body.addClass('loading');
    },

    stopLoader: function() {
        this.loading = false;
        this.ui.body.removeClass('loading');
    }

});

App.addInitializer(function () {

    App.elections = new Elections();
    App.formView = new FormView({collection: App.elections});
    App.moreView = new MoreView({collection: App.elections});

    App.form.show(App.formView);
    App.list.show(new ElectionsView({collection: App.elections}));
    App.more.show(App.moreView);

});

