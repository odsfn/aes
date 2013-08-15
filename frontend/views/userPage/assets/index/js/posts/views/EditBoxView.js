/* 
 * Basic class for adding, editing posts and comments.
 */
var EditBoxView = Marionette.ItemView.extend({
    
    placeholderText: 'What\'s  new?',
    
    buttonText: 'Post',
    
    template: '#edit-box-tpl',
    
    initialize: function(options) {
        _.extend(this, _.pick(options, 'placeholderText', 'buttonText'));
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
        if(this.ui.input.val() === '')
            this.simplify();
    },
            
    serializeData: function() {
        return _.extend(Marionette.ItemView.prototype.serializeData.call(this), {
            view: {
                placeholderText: this.placeholderText,
                buttonText: this.buttonText
            }
        });
    },
            
    onPost: function() {
        var value = this.ui.input.val();
        if(value !== '') {
            this.ui.postBtn.bButton().bButton('loading');
            this.ui.input.attr('disabled', 'disabled');
            this.model.set('content', value);
            this.trigger('edited', this.model);
        }
    },
            
    onCancel: function() {
        var value = this.ui.input.val();
        if(value !== '' && !confirm(i18n.t('All entered text will be lost. Are you sure that you want to cancel?'))) {
            return;
        }
        
        this.reset();
    },
            
    reset: function() {
        this.ui.input.val('');
        this.simplify();
    }
});

