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
        this.ui.body.addClass('hovered');
    },
            
    onMouseLeave: function() {
        this.ui.body.removeClass('hovered');
    },
            
    onRender: function() {
        if(this.commentsView) {
            this.ui.comments.append(this.commentsView.render().$el);
        }
    }
});

/* 
 * Renders the user's post collection.
 */
var PostsView = Marionette.CollectionView.extend({
   itemView: PostView,
   
   appendHtml: function(collectionView, itemView, index){
       if(index == 0) {
           collectionView.$el.prepend(itemView.el);
       }else{
           collectionView.$el.append(itemView.el);    
       }
   }
});

