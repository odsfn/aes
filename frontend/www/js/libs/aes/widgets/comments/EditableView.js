/* 
 * This is the strategy for Marionette.ItemView that provides functionality of
 * removing and inline editing of views model
 *  
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var EditableView = Marionette.ItemView.extend({
    
    tagName: 'span',
    
    className: 'controls pull-right',
    
    template: '#editable-tpl',
    
    initialize: function(options) {
        
        var defaults = {
            appendTo: 'h5.media-heading:first',
            askDeleteMsg: 'You are going to delete the record. Are you sure?',
        };
        
        _.defaults(options, defaults);
        
        _.extend(this, _.pick(options, _.keys(defaults)));
        
        this.base = options.view;
        
        this.model = this.base.model;
        
        this.listenTo(this.base, 'render', _.bind(function() {
            this.render();
            this.delegateEvents();
            $(this.appendTo, this.base.$el).append(this.$el);
        }, this));
    },
            
    ui: {
        editBtn: 'i.icon-pencil',
        deleteBtn: 'i.icon-remove'
    },
    
    events: {
        'click i.icon-remove': 'onRemoveClicked',
        'click i.icon-pencil': 'onEditBtnClicked'
    },
    
    onRemoveClicked: function() {
        if(confirm(i18n.t(this.askDeleteMsg))){
            this.delete();
        }
    },
            
    onEditBtnClicked: function() {
        var editBox = new EditBoxView({
            editingView: this.base
        });
        
        editBox.open();
        
        this.listenToOnce(editBox, 'edited', function() {
            editBox.model.save({}, {
                success: _.bind(function() {
                    editBox.close();
                    this.base.render();
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
        if(WebUser.isGuest()) {
            this.$el.hide();
            
        //Authenticated but post made by other user
        } else if(WebUser.getId() != this.model.get('user_id')) {
            //on the current user's page
            if(true || WebUser.getId() == PostsApp.pageUserId)
               //current user can't edit posts made by others
               this.ui.editBtn.remove();
            else    //on page of another user
               this.$el.hide(); 
        }
    }
});

