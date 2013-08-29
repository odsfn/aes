/* 
 * View for user's page feed title
 */
var PostsTitleView = Marionette.ItemView.extend({
    
    template: '#posts-title-tpl',
    
    ui: {
        authorSwitcher: 'small.author-switcher a'
    },
    
    events: {
        'click small.author-switcher a': 'switchAuthor'
    },
    
    initialize: function(options) {
        this.model = new Backbone.Model({
            count: 0,
            allUsers: true,
            switcherText: ''
        });
        
        this.listenTo(this.model, 'change:count change:allUsers', this.render);
    },
    
    setRecordsCount: function(count) {
        this.model.set('count', count);
    },
            
    switchAuthor: function() {
        this.model.set('allUsers', !this.model.get('allUsers'));
        
        if(!this.model.get('allUsers')) {
            PostsApp.Feed.posts.setFilter('usersRecordsOnly', PostsApp.pageUserId);
        }else{
            PostsApp.Feed.posts.setFilter('usersRecordsOnly', false);
        }
    },
            
    onBeforeRender: function() {
        if(this.model.get('allUsers')) {
            this.model.set('switcherText', 'Show users\' records only');
        }else{
            this.model.set('switcherText', 'Show all records');
        }
    }
});

