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
                reply_to: this.model.get('post').get('id')
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

