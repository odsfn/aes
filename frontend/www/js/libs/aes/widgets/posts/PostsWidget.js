/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var PostsWidget = {};

//PostsWidget.SomePublicClass = {foo: 'Baz'};

_.extend(PostsWidget, (function(){
    
    var Post = Backbone.Model.extend({
        defaults: {
            reply_to: null,
            user_id: null,
            target_id: null,
            user: {
                user_id: null,
                photo: '',
                displayName: '',
            },
            content: '',
            likes: null,
            dislikes: null,
            displayTime: null,
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
            comments: 'div.comments', 
            ratePlus: '.post-rate:first span.icon-thumbs-up',
            rateMinus: '.post-rate:first span.icon-thumbs-down'
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

        render: function() {
            console.log('In post render');
            return Marionette.ItemView.prototype.render.apply(this, arguments);
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
            this.collection.reset(this.model.get('post').get('comments'));
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
           
        onRender: function() {
            
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
    
    return {
        create: function(options) {
//            console.log(PostsWidget.SomePublicClass.foo);

            var view = new PostsLayout();
            view.posts.targetId = options.targetId;
            view.posts.userPageId = options.userPageId;
            view.posts.limit = options.limit || 20;
            return view;
        }
    };
    
    
})());