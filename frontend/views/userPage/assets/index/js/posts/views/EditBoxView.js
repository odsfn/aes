/* 
 * Basic class for adding, editing posts, comments, etc.
 * 
 * To edit existing model you should provide editiongView property during 
 * instantiation of the new EditBoxView. This view should contain model property.
 */
var EditBoxView = Marionette.ItemView.extend({
    
    placeholderText: 'What\'s  new?',
    
    buttonTextCreate: 'Post',
    
    buttonTextUpdate: 'Update',
    
    template: '#edit-box-tpl',
    /**
     * Model's attribute which will be edited by EditBox
     * 
     * @type String 
     */
    editingAttr: 'content',
    
    editingView: null,
    
    initialize: function(options) {
        _.extend(this, _.pick(options, 
            'placeholderText', 'buttonTextCreate', 'buttonTextUpdate', 'editingView', 'editingAttr'
        ));
        
        if(this.editingView) {
            this.model = this.editingView.model;
        }
    },
    
    ui: {
        postBtn: 'button.post',
        cancelBtn: 'button.cancel',
        inputHolder: 'input',
        input: 'textarea',
        body: 'div.body',
        controls: 'div.controls'
    },
    
    events: {
        'focusin input': 'open',
        'focusout textarea': 'onFocusOut',
        'click button.post': 'onPost',
        'click button.cancel': 'onCancel'
    },
    
    open: function() {
        if(this.editingView) {
            this.render();
            this.editingView.$el.hide();
            this.editingView.$el.after(this.el);
        }
    
        var body = this.ui.body;
        
        if(!body.hasClass('well')) {
            body.addClass('well');
            this.ui.inputHolder.hide();
            this.ui.input.show();
            this.ui.input.focus();
            this.showControls();
	}
    },
            
    showControls: function() {
        this.ui.controls.show();
    },
            
    simplify: function() {
        this.ui.input.hide();
        this.ui.inputHolder.show();
        this.ui.controls.hide();
        this.ui.body.removeClass('well');
    },
            
    onFocusOut: function() {
        if(!this.editingView && this.ui.input.val() === '')
            this.simplify();
    },
            
    serializeData: function() {
        var buttonText = this.buttonTextCreate;
        if(this.editingView) {
            buttonText = this.buttonTextUpdate;
        }
        
        return _.extend(Marionette.ItemView.prototype.serializeData.call(this), {
            view: {
                placeholderText: this.placeholderText,
                buttonText: buttonText
            }
        });
    },
            
    onPost: function() {
        var value = this.ui.input.val();
        if(value !== '' && value !== this.model.get(this.editingAttr)) {
            this.ui.postBtn.bButton().bButton('loading');
            this.ui.input.attr('disabled', 'disabled');
            this.model.set(this.editingAttr, value);
            this.trigger('edited', this.model);
        }
    },
            
    onCancel: function() {
        var value = this.ui.input.val();

        if(value !== '' 
            && ( (!this.editingView && !confirm(i18n.t('All entered text will be lost. Are you sure that you want to cancel?')))
                || (this.editingView && this.model.get(this.editingAttr) !== value 
                && !confirm(i18n.t('All changes will be lost. Are you sure that you want to cancel?')))
               )
        ) return;
        
        if(!this.editingView)
            this.reset();
        else{
            this.close();
        }
    },
    
    onBeforeClose: function() {
        if(this.editingView)
            this.editingView.$el.show();
    },
            
    reset: function() {
        this.ui.input.val('');
        this.simplify();
    },
            
    onRender: function() {
        //Don't show for unauthenticated
        if(webUser.isGuest())
            this.$el.hide();
    }
});

