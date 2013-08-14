/* 
 * Basic class for adding, editing posts and comments
 */
var EditBoxView = Marionette.ItemView.extend({
    
    placeholderText: 'What\'s  new?',
    
    buttonText: 'Post',
    
    template: '#edit-box-tpl',
    
    initialize: function(options) {
        console.log(JSON.stringify(options));
        _.extend(this, _.pick(options, 'placeholderText', 'buttonText'));
    },
    
    ui: {
        postBtn: 'button',
        input: 'input',
        body: 'div.body',
        controls: 'div.controls'
    },
    
    events: {
        'focusin input': 'open',
        'focusout input': 'onFocusOut'
    },
    
    open: function() {
        var body = this.ui.body;
        
        if(!body.hasClass('well')) {
            body.addClass('well');
            this.showControls();
	}
    },
            
    showControls: function() {
        this.ui.controls.show();
    },
            
    simplify: function() {
        this.ui.body.removeClass('well'),
        this.ui.controls.hide();        
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
    }
    
});

