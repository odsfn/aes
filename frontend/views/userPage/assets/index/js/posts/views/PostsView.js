/**
 * Renders single post item with like, dislike, edit, delete buttons.
 */
var PostView = Marionette.ItemView.extend({
    className: 'media post',
    template: '#post-tpl'
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

