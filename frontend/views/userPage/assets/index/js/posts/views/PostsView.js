/**
 * Renders single post item with like, dislike, edit, delete buttons.
 */
var PostView = Marionette.ItemView.extend({
        
    className: 'media post',
    
    template: '#post-tpl',
    
    ui: {
        topControls: 'span.controls',
        editBtn: 'span.controls i.icon-pencil',
        rates: '.post-rate',
        body: '.media-body'
    },
    
    events: {
        'mouseenter div.media-body': 'onMouseEnter',
        'mouseleave div.media-body': 'onMouseLeave',
        'click span.controls i.icon-remove': 'onRemoveClicked',
        'click span.controls i.icon-pencil': 'onEditBtnClicked'
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
            
    onEditBtnClicked: function() {
        var editBox = new EditBoxView({
            editingView: this
        });
        
        editBox.open();
        
        this.listenToOnce(editBox, 'edited', function() {
            editBox.model.save({}, {
                success: _.bind(function() {
                    editBox.close();
                    this.render();
                }, this),
                wait: true
            });
        });
    },
            
    delete: function() {
        this.model.destroy();
    },
            
    onRender: function() {
        //Checking for available actions for current user
        if(webUser.isGuest()) {
            
            //Not authenticated
            this.ui.topControls.remove();
        
        //Authenticated but post made by other user
        } else if(webUser.id != this.model.get('authorId')) {
            //on the current user's page
            if(webUser.id == PostsApp.pageUserId)
               //current user can't edit posts made by others
               this.ui.editBtn.remove();
            else    //on page of another user
               this.ui.topControls.remove(); 
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

