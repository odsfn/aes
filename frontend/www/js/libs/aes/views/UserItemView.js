/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.UserItemView = Aes.ItemView.extend({
    
    className: 'user-info',
    
    getTplStr: function() {
        return Aes.UserItemView.getTpl();
    },

    ui: {
        controls: 'span.controls'
    },

    triggers: {
        'mouseenter': 'mouseEnter',
        'mouseleave': 'mouseLeave'
    },

    controls: null,

    getControls: function() {
        return {};
    },

    onMouseEnter: function() {
        this.ui.controls.show();
    },

    onMouseLeave: function() {
        this.ui.controls.hide();
    },
            
    onRender: function() {
        if(this.controls) {
            for(var controlName in this.controls) {
                var curControl = this.controls[controlName];
                if(curControl.shouldBeShown(this)) 
                {
                    curControl.render();
                    this.ui.controls.append(curControl.$el);
                    curControl.delegateEvents();
                }
            }
        }
    },
    
    onShow: function() {
        if(this.controls) {
            for(var controlName in this.controls) {
                var curControl = this.controls[controlName];
                if(curControl.shouldBeShown(this))
                {
                    curControl.trigger('show');
                }
            }
        }
    },
            
    initialize: function(options) {
        var controlsSpec = this.getControls() || {};
        var controls = {};
        for(var controlName in controlsSpec) {
            var controlSpec = controlsSpec[controlName];
            var spec = controlSpec;
            
            _.extend(spec, {parent: this});
            
            if(controlSpec.class)
                controls[controlName] = new controlSpec.class(spec);
            else
                controls[controlName] = new Aes.UserItemView.SmallControl(spec);
            
        }
        
        if(options.controls) {
            
            for(var controlName in options.controls) {
                var control = options.controls[controlName];
                control.setParent(this);
                controls[controlName] = control;
            }
        }
        
        this.controls = controls;
    }
}, {
    getTpl: function() {
        return '<div class="pull-left">'
            + '<div style="width: 96px; height: 96px; background-color: #000;" class="img-wrapper-tocenter users-photo">'
                + '<span></span>'
                + '<img alt="<%= displayName %>" src="<%= photoThmbnl64 %>">'
            + '</div>'
        + '</div>'

        + '<div class="pull-right right-top-panel">'
            + '<span class="controls"></span>'
        + '</div>'                        

        + '<div class="body">'
            + '<a href="<%= pageUrl %>"><%= displayName %></a> <br>'

            + '<div><b>Birth Day: </b><%= i18n.date(birth_day, \'full\') %></div>'

            + '<div><b>Birth Place: </b><%= birth_place %></div>'
        + '</div>';
    }
});

Aes.UserItemView.SmallControl = Aes.ItemView.extend({
    getTplStr: function() {
        return Aes.UserItemView.SmallControl.getTpl();
    },
            
    text: '',
    iconType: '',
    
    _parent: null,
    
    tagName: 'small',
    
    triggers: {
        'click': 'click'
    },
            
    onClick: function() {
        this.triggerMethod('before:run');
        this.run();
        this.triggerMethod('after:run');
    },

    onBeforeShow: function() {
        return true;
    },            
            
    run: function() {
        this.triggerMethod('run');
    },
    
    setParent: function(parent) {
        this._parent = parent;
    },
            
    shouldBeShown: function() {
        return this.triggerMethod('before:show', this._parent);
    },
            
    initialize: function(options) {
        _.extend(this, _.pick(options, 'text', 'iconType', 'onRun', 'onBeforeRun', 'onAfterRun', 'onBeforeShow'));
        
        this.model = new Backbone.Model({
            text: this.text,
            iconType: this.iconType
        });
        
        if(options.parent)
            this.setParent(options.parent);
    }
}, {
    getTpl: function() {
        return '<%= text %>&nbsp;<i class="icon-<%= iconType %>"></i>';
    }
});