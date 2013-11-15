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
        if(!WebUser.hasAccess('EditableView.show', this))
            this.$el.hide();
        
        if(!WebUser.hasAccess('EditableView.edit', this))
            this.ui.editBtn.remove();
        
        if(!WebUser.hasAccess('EditableView.delete', this))
            this.ui.deleteBtn.remove();
    }
});

