/**
 * Comments Widget Factory to the certain entity. 
 *   
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var CommentsWidget = (function(){

    var Comment = Backbone.Model.extend({
        
        defaults: {
            target_id: null,
            user_id: null,
            user: {
                user_id: null,
                photo: '',
                displayName: ''
            },
            content: '',
            likes: null,
            dislikes: null,
            rates: []
        },

        initialize: function(attrs, options) {
            
            if(!options) 
                return;
    
            var ratesModels = this.get('rates');

            this.rates = options.commentRates;

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
    
    var Comments = FeedCollection.extend({
        
       comparator: function(model) {
           return model.get('created_ts');
       },        
        
       model: function(attrs, options) {
           return new Comment(attrs, _.extend(options, {
               commentRates: options.collection.createCommentRates()
           }));
       },
               
       createCommentRates: function() {
            
            var commentRates = new Rates([], {
                url: this.commentRatesUrl
            });
            
            return commentRates;
       },
               
       initialize: function(models, options) {
            FeedCollection.prototype.initialize.apply(this, arguments);
            
            this.url = options.url;
            this.commentRatesUrl = options.commentRatesUrl;
            
            this.targetId = options.targetId;
            
            this.filters = {
                target_id: this.targetId
            };
       }
    });    
    
    /**
     * Renders single comment item with like, dislike, edit, delete buttons.
     */
    var CommentView = Marionette.ItemView.extend({

        className: 'media post',

        template: '#post-tpl',

        _user: null,

        initialize: function(options) {

            this._user = options.user;

            this.strategies = {
                editable: new EditableView({view: this})
            };
            
            this.ratesView = new RatesWidget.create({
                
                targetId: this.model.id,
                
                targetType: 'ElectionComment',
                
                urls: {
                    rates: options.ratesUrl
                },
  
                ratesCollection: this.model.rates
                
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
            if(this._user.hasAccess('CommentView.showControls', this))
                this.ui.body.addClass('hovered');
        },

        onMouseLeave: function() {
            if(this._user.hasAccess('CommentView.showControls', this))
                this.ui.body.removeClass('hovered');
        },

        onRender: function() {

             this.ui.rates.html(this.ratesView.render().el);
             this.ratesView.delegateEvents();
             
        }
    });
    
    /* 
     * Comments list with ability to add new comments for any post
     * 
     * @author Vasiliy Pedak <truvazia@gmail.com>
     */
    var CommentsView = Marionette.CompositeView.extend({

        itemView: CommentView,

        template: '#comments-tpl',

        itemViewContainer: 'div.comments-feed',

        ui: {
            feed: 'div.comments-feed',
            newCommentContainter: 'div.comment-to-comment'
        },

        buildItemView: function(item, ItemViewType, itemViewOptions) {
            if(ItemViewType === Marionette.getOption(this, 'emptyView'))
                return new ItemViewType();
            
            return Marionette.CompositeView.prototype.buildItemView.apply(this, arguments);
        },

        onRender: function() {
            this.newCommentRegion = new Marionette.Region({el: this.ui.newCommentContainter});
            this.resetNewCommentRegion();
        },

        initNewCommentView: function() {
            this.newCommentView = new EditBoxView({
                placeholderText: 'Comment...',
                model: new Comment({
                    target_id: this.collection.targetId
                },{
                    commentRates: this.collection.createCommentRates()
                })
            });

            this.listenTo(this.newCommentView, 'edited', this.addComment);
        },

        resetNewCommentRegion: function() {
            this.initNewCommentView();
            this.newCommentRegion.show(this.newCommentView);
        },

        addComment: function(comment) {
            this.collection.create(comment, {
                success: _.bind(function() {
                   this.resetNewCommentRegion();
                }, this),
                wait: true
            });
        },        
        
        appendHtml: function(collectionView, itemView){
            collectionView.ui.feed.append(itemView.el);
        }
    });    
    
    var Composite = Marionette.View.extend({
        
        initialize: function() {
            this.children = new Backbone.ChildViewContainer();
        },
                
        render: function() {
            
            this.isClosed = false;

            this.triggerMethod("before:render", this);

            this.children.each(function(view) {
                this.$el.append(view.render().$el);
            }, this);

            this.bindUIElements();

            this.triggerMethod("render", this);

            return this;            
            
        },
        
        onShow: function() {
    
            this.children.each(function(view) {
                view.triggerMethod('show');
            });
            
        }
        
    });
    
    var LoadBtnView = MoreView.extend({
        template: '#load-msg-btn-tpl',
        ui:{
            body: 'button'
        }
    });
    
    var FeedTitleView = Marionette.ItemView.extend({
        
        template: '#feed-title-layout-tpl',
        
        ui: {
            feedCount: '.msgs-count',
            loadBtn: 'li.load-btn-cnt',
        },
        
        initialize: function(options) {
            this.feedView = options.feedView || false;
        },
        
        onRender: function() {
            this.moreView = new LoadBtnView({
                appendTo: this.ui.loadBtn,
                view: this.feedView
            });
            
            this.listenTo(this.moreView, 'loaded', function() {
                this.feedView.render();
            });
        },

        onShow: function() {
            
            this.feedCountView = new FeedCountView({
                el: this.ui.feedCount,
                feed: this.feedView.collection
            });
            
        }
    });
    
    var defaultConfig = {

        targetId: null,
        
        targetType: null,
        
        webUser: WebUser || null,
        
        urlManager: UrlManager || null, 
        
        templates: {
            commentsView: '#comments-tpl',
            commentView: '#post-tpl'
        },

        initData: {
            totalCount: 0,
            models: []
        },
        
        autoFetch: true,
        
        urls: {
            comments: null,
            
            rates: null
        },
        
        limit: 20,
        
        title: false
        
    };
    
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
               return ( this.getId() == view.model.get('user_id') || this.hasRole('commentsAdmin') );
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
            
            var view, config, comments, urlManager;
            
            config = _.extend({}, defaultConfig, options);
            
            urlManager = config.urlManager;
            
            if(!config.urls.comments)
                config.urls.comments = urlManager.createUrl('api/' + config.targetType + '_comment');
            
            if(!config.urls.rates)
                config.urls.rates = urlManager.createUrl('api/' + config.targetType + 'Comment_rate');
            
            //initializing collection
            comments = new Comments([], {
                targetId: config.targetId,
                targetType: config.targetType,
                url: config.urls.comments,
                commentRatesUrl: config.urls.rates
            });
            
            if(config.limit) {
                comments.limit = config.limit;
            }
            
            if(config.initData.models.length > 0)   //setting up init data rows if any provided
                comments.reset(config.initData.models, {parse: true, totalCount: config.initData.totalCount});
            
            var commentsViewOptions = {
                
                template: config.templates.commentsView,
                
                collection: comments,
                
                itemViewOptions: {
                    template: config.templates.commentView,
                    ratesUrl: config.urls.rates,
                    user: config.webUser
                }
            };
            
            if (config.emptyView) {
                var emptyView;
                
                if (_.isFunction(config.emptyView))
                    emptyView = config.emptyView;
                else
                    emptyView = Aes.NoItemView;
                
                commentsViewOptions.emptyView = emptyView;
            }
            
            var commentsView = view = new CommentsView(commentsViewOptions);
            
            if(config.autoFetch && config.initData.models.length == 0)            
                commentsView.once('show', function() {
                   this.collection.fetch({
                       success: function() {
                            commentsView.render();
                       },
                       silent: true
                   });
                });
                
            if(config.title) {
                var titleView = new FeedTitleView({
                    feedView: view
                });
                
                view = new Composite();
                view.children.add(titleView);
                view.children.add(commentsView);
            }
            
            return view;
        }
    };
    
})();

