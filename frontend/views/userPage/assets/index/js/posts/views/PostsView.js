/**
 * Renders single post item with like, dislike, edit, delete buttons.
 */
var PostView = Marionette.ItemView.extend({
        
    className: 'media post',
    
    template: '#post-tpl',
    
    initialize: function() {

        if(!this.model.get('reply')) {
            this.commentsView = new CommentsView({
                model: new Backbone.Model({post: this.model})
            });
        }
        
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
        if(!webUser.isGuest())
            this.ui.body.addClass('hovered');
    },

    onMouseLeave: function() {
        if(!webUser.isGuest())
            this.ui.body.removeClass('hovered');
    },
    
    onRatePlus: function() {
        this.rate('up');
    },
            
    onRateMinus: function() {
        this.rate('down');
    },
            
    onRender: function() {
        if(this.commentsView) {
            this.ui.comments.append(this.commentsView.render().$el);
        }
        
        var rate;
        //Mark user's vote
        if(!webUser.isGuest() && (rate = this.model.rates.getRate(webUser.id))) {
             if(rate.get('score') == 1) {
                 this.ui.ratePlus.addClass('chosen');
             }else{
                 this.ui.rateMinus.addClass('chosen');
             }
        }
    },
            
    rate: function(score) {
        
        if(webUser.isGuest())
            return;
        
        if(score === 'up') {
            score = 1;
        }else{
            score = -1;
        }
        
        var 
            lastRate = this.model.rates.getRate(webUser.id),
            lastRateScore = null;
    
        if(lastRate) {
            lastRate.destroy();
            lastRateScore = lastRate.get('score');
        }
        
        if(lastRateScore != score) {    
            this.model.rates.addRate(webUser.id, score);
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

