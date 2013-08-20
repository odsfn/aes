/**
 * Renders single post item with like, dislike, edit, delete buttons.
 */
var PostView = Marionette.CompositeView.extend({
        
    className: 'media post',
    
    template: '#post-tpl',
    
    itemViewContainer: 'div.comments',
    
    initialize: function() {
        this.collection = new Comments();
        this.collection.reset(this.model.get('comments'));
        
        this.strategies = {
            editable: new EditableView({view: this})
        };
    },
            
    ui: {
        rates: '.post-rate',
        body: '.post-body'
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

