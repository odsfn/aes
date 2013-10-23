/**
 * Comments Widget Factory to the certain entity. 
 *   
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var CommentsWidget = (function(){

    var CommentRate = Backbone.Model.extend({
        
        defaults: {
            score: null,
            target_id: null,
            user_id: null,
            createdTs: null
        },
                
        /**
         * @TODO: implement this
         */        
        parse: function(rawData) {
            
            rawData = Backbone.Model.prototype.parse.apply(this, arguments)
            
            rawData.created_ts = parseInt(rawData.created_ts) * 1000;
            
            var toInt = ['id', 'score', 'target_id', 'user_id'];
            
            for(var i = 0; i < toInt.length; i++) {
                rawData[toInt[i]] = parseInt(rawData[toInt[i]]);
            }
            
            return rawData;
        }                
    });
    
    var CommentRates = Backbone.Collection.extend({

        target_id: null,

        model: CommentRate,

        initialize: function(models, options) {
            var options = options || {};

            this.url = options.url;

            _.defaults(options, {
                target_id: null
            });

            _.extend(this, _.pick(_.keys(options)));
        },

        getLikes: function() {
            return this.where({score: 1}).length;
        },

        getDislikes: function() {
            return this.where({score: -1}).length;
        },

        addRate: function(user_id, score) {
            return this.create({
               target_id: this.target_id,
               user_id: user_id,
               score: score
            });
        },        

        getRate: function(user_id) {
            return this.findWhere({user_id: user_id});
        }
    });    
    
    var Comment = Backbone.Model.extend({
        
        defaults: {
            target_id: null,
            user_id: null,
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
            
            rawData = Backbone.Model.prototype.parse.apply(this, arguments)
            
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
       model: function(attrs, options) {
           return new Comment(attrs, _.extend(options, {
               commentRates: options.collection.createCommentRates()
           }));
       },
               
       createCommentRates: function() {
            var commentRates = new CommentRates([], {
                url: this.commentRatesUrl
            });
            
            return commentRates;
       },
               
       initialize: function(models, options) {
            FeedCollection.prototype.initialize.apply(this, arguments);
            
            this.url = options.url;
            this.commentRatesUrl = options.commentRatesUrl;
            
            this.targetId = options.targetId;
            
            this.filter = {
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

            this.listenTo(this.model.rates, 'add', _.bind(function(rate, collection){
                if(rate.get('score') == 1) {
                    this.ui.ratePlus.addClass('chosen');
                }
                else
                    this.ui.rateMinus.addClass('chosen');

                this.updateRates();
            }, this));

            this.listenTo(this.model.rates, 'remove', _.bind(function(rate, collection){
                if(rate.get('score') == 1) {
                    this.ui.ratePlus.removeClass('chosen');
                }
                else
                    this.ui.rateMinus.removeClass('chosen');

                this.updateRates();
            }, this));
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
            'mouseleave div.post-body:first': 'onMouseLeave',
            'click .post-rate:first span.icon-thumbs-up': 'onRatePlus',
            'click .post-rate:first span.icon-thumbs-down': 'onRateMinus'
        },

        onMouseEnter: function() {
            if(!this._user.isGuest())
                this.ui.body.addClass('hovered');
        },

        onMouseLeave: function() {
            if(!this._user.isGuest())
                this.ui.body.removeClass('hovered');
        },

        onRatePlus: function() {
            this.rate('up');
        },

        onRateMinus: function() {
            this.rate('down');
        },

        onRender: function() {

            var rate;
            //Mark user's vote
            if(!this._user.isGuest() && (rate = this.model.rates.getRate(this._user.id))) {
                 if(rate.get('score') == 1) {
                     this.ui.ratePlus.addClass('chosen');
                 }else{
                     this.ui.rateMinus.addClass('chosen');
                 }
            }
        },

        rate: function(score) {

            if(this._user.isGuest())
                return;

            if(score === 'up') {
                score = 1;
            }else{
                score = -1;
            }

            var 
                lastRate = this.model.rates.getRate(this._user.id),
                lastRateScore = null;

            if(lastRate) {
                lastRate.destroy();
                lastRateScore = lastRate.get('score');
            }

            if(lastRateScore != score) {    
                this.model.rates.addRate(this._user.id, score);
            }       

        },

        updateRates: function() {
            this.ui.ratePlus.html(this.model.rates.getLikes());
            this.ui.rateMinus.html(this.model.rates.getDislikes());
        },

        serializeData: function() {
            var serializedData = Marionette.ItemView.prototype.serializeData.apply(this, arguments);

            _.extend(serializedData, {
                likes: this.model.rates.getLikes(),
                dislikes: this.model.rates.getDislikes()
            });

            return serializedData;
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

        onRender: function() {
            this.newCommentRegion = new Marionette.Region({el: this.ui.newCommentContainter});
            this.resetNewCommentRegion();
        },

        initNewCommentView: function() {
            this.newCommentView = new EditBoxView({
                placeholderText: 'Comment...',
                model: new Comment({
                    target_id: this.collection.targetId,
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
        }            
    });    
    
    var defaultConfig = {

        targetId: null,
        
        targetType: null,
        
        webUser: null,
        
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
        
        pagination: null
        
    };
    
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
            
            if(config.initData.models.length > 0)   //setting up init data rows if any provided
                comments.reset(config.initData.models, {parse: true, totalCount: config.initData.totalCount});
                
            
            view = new CommentsView({
                
                template: config.templates.commentsView,
                
                collection: comments,
                
                itemViewOptions: {
                    template: config.templates.commentView,
                    
                    user: webUser
                }
                
            });
            
            if(config.autoFetch && config.initData.models.length == 0)            
                view.once('show', function() {
                   this.collection.fetch(); 
                });
            
            return view;
        }
    };
    
})();

