/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var PostsWidget = {};

_.extend(PostsWidget, (function(){
    
    var Post = Backbone.Model.extend({
        defaults: {
            reply_to: null,
            user_id: null,
            target_id: null,
            created_ts: null,
            last_update_ts: null,
            user: {
                user_id: null,
                photo: '',
                displayName: '',
            },
            content: '',
            likes: null,
            dislikes: null,
            comments: []
        },

        urlRoot: UrlManager.createUrlCallback('api/post'),

        initialize: function() {
            var ratesModels = this.get('rates') || [];

            this.rates = new Rates([], {
                target_id: this.get('id'),
                url: UrlManager.createUrl('api/Post_rate')
            });

            if(ratesModels) {
                this.rates.reset(ratesModels, {parse: true});
            }

            if(this.id) {
                this.rates.target_id = this.id;
            }

            this.on('change:id', _.bind(function(){
                this.rates.target_id = this.id;
            }, this));
        },
                
        parse: function(rawData) {
            
            rawData = _.clone(rawData);
            
            rawData = Backbone.Model.prototype.parse.apply(this, arguments);
            
            rawData.created_ts = parseInt(rawData.created_ts) * 1000;
            
            if(rawData.last_update_ts > 0)
                rawData.last_update_ts = parseInt(rawData.last_update_ts) * 1000;
            
            return rawData;
        },
                
        toJSON: function(options) {
            
            var attrs = Backbone.Model.prototype.toJSON.call(this);
            
            if(options && _.has(options, 'success')) {
                var created_ts, last_update_ts;
                
                if(created_ts = this.get('created_ts'))
                    attrs.created_ts = created_ts.toString().substr(0, 10);
                
                if(last_update_ts = this.get('last_update_ts'))
                    attrs.last_update_ts = last_update_ts.toString().substr(0, 10);
            }
            
            return attrs;
        }

    });    
    
    var Posts = FeedCollection.extend({
        targetId: null,
        userPageId: null,
        model: Post,
        url: UrlManager.createUrlCallback('api/post'),
        getFilters: function() {
            return {
                userPageId: this.userPageId,
                targetId: this.targetId
            };
        }
    });    
    
    /* 
     * Collection for posts that are displayed as comments
     */
    var Comments = Backbone.Collection.extend({
       model: Post,
       url: UrlManager.createUrlCallback('api/post')
    });    
    
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
            
            this.postsCol = options.postsCol;
    
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
                this.postsCol.setFilter('usersRecordsOnly', this.postsCol.userPageId);
            }else{
                this.postsCol.setFilter('usersRecordsOnly', false);
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
    
    /**
     * Renders single post item with like, dislike, edit, delete buttons.
     */
    var PostView = Marionette.ItemView.extend({

        className: 'media post',

        template: '#post-tpl',

        initialize: function() {
            
            if(!this.model.get('reply_to')) {
                this.commentsView = new CommentsView({
                    model: new Backbone.Model({post: this.model})
                });
            }

            this.strategies = {
                editable: new EditableView({view: this})
            };
            
            this.ratesView = new RatesWidget.create({
                
                targetId: this.model.get('id'),
  
                ratesCollection: this.model.rates,
  
                autoFetch: false
            });            
            
        },

        ui: {
            rates: '.post-rate',
            body: '.post-body',
            comments: 'div.comments'
        },

        events: {
            'mouseenter div.post-body:first': 'onMouseEnter',
            'mouseleave div.post-body:first': 'onMouseLeave'
        },

        onMouseEnter: function() {
            if(!WebUser.isGuest())
                this.ui.body.addClass('hovered');
        },

        onMouseLeave: function() {
            if(!WebUser.isGuest())
                this.ui.body.removeClass('hovered');
        },

        onRender: function() {
            if(this.commentsView) {
                this.ui.comments.append(this.commentsView.render().$el);
            }

            this.ui.rates.html(this.ratesView.render().el);
            this.ratesView.delegateEvents();
            this.ratesView.bindEventsToTarget($('.post-body:first', this.$el));

        }
        
    });

    /* 
     * Comments list with ability to add new comments for any post
     * 
     * @author Vasiliy Pedak <truvazia@gmail.com>
     */
    var CommentsView = Marionette.CompositeView.extend({

        itemView: PostView,

        template: '#comments-tpl',

        itemViewContainer: 'div.comments-feed',

        ui: {
            feed: 'div.comments-feed',
            newCommentContainter: 'div.comment-to-comment'
        },

        initialize: function() {
            this.collection = new Comments();
            this.collection.reset(this.model.get('post').get('comments'), {parse: true});
        },

        onRender: function() {
            this.newCommentRegion = new Marionette.Region({el: this.ui.newCommentContainter});
            this.resetNewCommentRegion();
        },

        initNewCommentView: function() {
            this.newCommentView = new EditBoxView({
                placeholderText: 'Comment...',
                model: new Post({
                    reply_to: this.model.get('post').get('id'),
                    target_id: this.model.get('post').get('target_id')
                })
            });

            this.listenTo(this.newCommentView, 'edited', this.addComment);
        },

        resetNewCommentRegion: function() {
            this.initNewCommentView();
            this.newCommentRegion.show(this.newCommentView);
        },

        addComment: function(post) {
            this.collection.create(post, {
                success: _.bind(function() {
                   this.resetNewCommentRegion();
                }, this),
                wait: true
            });
        }            
    });

    /* 
     * Renders the user's post collection.
     */
    var PostsView = Marionette.CollectionView.extend({
       itemView: PostView,

       initialize: function() {
           this.strategies = {
               feed: new MoreView({
                   view: this,
                   appendTo: '#posts-load-btn'
               })
           };
       },

       appendHtml: function(collectionView, itemView, index){
           if(index == 0) {
               collectionView.$el.prepend(itemView.el);
           }else{
               collectionView.$el.append(itemView.el);    
           }
       },
    });    
    
    
    var PostsLayout = Marionette.Layout.extend({
        
        template: '#posts-layout',
        
        regions: {
            newPostRegion: '#add-post-top',
            feedTitleRegion: '#feed-title',
            postsRegion: '#posts-feed'
        },
                
        resetNewPostRegion: function() {
            this.initAddPostView();
            this.newPostRegion.show(this.addPostView);        
        },

        addPost: function(post) {
            this.posts.create(post, {
                success: _.bind(function() {
                   this.resetNewPostRegion();
                }, this),
                wait: true
            });
        },

        initAddPostView: function() {
            this.addPostView = new EditBoxView({
                model: new Post({
                    target_id: this.posts.targetId
                })
            });

            this.listenTo(this.addPostView, 'edited', this.addPost);
        },
        
        onShow: function() {
            this.titleView = new PostsTitleView({
                postsCol: this.posts
            });
            this.feedTitleRegion.show(this.titleView);
            this.resetNewPostRegion();

            this.listenTo(this.posts, 'request', function() {
                $('#posts-app-container').mask();
            });

            this.listenTo(this.posts, 'sync remove add', _.bind(function(collection) {
                $('#posts-app-container').unmask();
            }, this));

            this.listenTo(this.posts, 'sync', function() {
                this.postsRegion.show(this.postsView);
            });

            this.listenTo(this.posts, 'totalCountChanged', _.bind(function(actualValue) {
                this.titleView.setRecordsCount(actualValue);
            }, this));

            this.posts.fetch();            
        },
                
        initialize: function() {
            /**
             * Posts collection displaying on the user's page
             * @type Posts
             */
            this.posts = new Posts();

            this.postsView = new PostsView({
                collection: this.posts
            });
        }
    });

    WebUser.addAccessRules({
        
       "EditBoxView": {
           "show": function(view) {
               return this.isAuthenticated();
           }
       },
               
       "EditableView": {
           "show": function(view) {
               return this.isAuthenticated();
           },
           "edit": function(view) {
               return ( this.getId() == view.model.get('user_id') );
           },
           "delete": function(view) {
               return ( this.getId() == view.model.get('user_id') || this.hasRole('postsAdmin') );
           }
       },
               
       "CommentView": {
           "showControls": function(view) {
               return this.isAuthenticated();
           }
       }
       
    });
    
    var defaultConfig = {

        targetId: null,
        
        userPageId: null,
        
        webUser: WebUser || null,
        
        urlManager: UrlManager || null, 
        
        templates: {
            postsLayout: '#posts-layout',
            postView: '#post-tpl',
            commentView: '#post-tpl',
            postTitleView: '#post-title-tpl',
            editBoxView: '#edit-box-tpl',
            editableView: '#editable-tpl',
            commentsView: '#comments-tpl',
            moreView: '#more-btn-tpl'
        },

//        initData: {
//            totalCount: 0,
//            models: []
//        },
//        
//        autoFetch: true,
//        
//        urls: {
//            comments: null,
//            
//            rates: null
//        },
        
        limit: 20
        
    };
    
    return {
        create: function(options) {
            
            var view, config;
            
            config = _.extend({}, defaultConfig, options);
            
            view = new PostsLayout({
                template: config.templates.postsLayout
            });
            
            view.posts.targetId = config.targetId;
            view.posts.userPageId = config.userPageId;
            view.posts.limit = config.limit;
            
            return view;
        }
    };
    
    
})());