/**
 * Renders single post item with like, dislike, edit, delete buttons.
 */
var PostView = Marionette.ItemView.extend({
        
    className: 'media post',
    
    template: '#post-tpl',
    
    ui: {
        topControls: 'span.controls',
        rates: '.post-rate',
        body: '.media-body'
    },
    
    events: {
        'mouseenter div.media-body': 'onMouseEnter',
        'mouseleave div.media-body': 'onMouseLeave',
        'click span.controls i.icon-remove': 'onRemoveClicked'
    },
    
    onMouseEnter: function() {
        this.ui.body.addClass('hovered');
    },
            
    onMouseLeave: function() {
        this.ui.body.removeClass('hovered');
    },
    
    onRemoveClicked: function() {
        if(confirm(i18n.t('You are going to delete the record. Are you sure?'))){
            this.delete();
        }
    },
            
    delete: function() {
        this.model.destroy();
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

