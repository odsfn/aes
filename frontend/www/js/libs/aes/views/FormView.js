/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = Aes || {};

Aes.FormField = Aes.ItemView.extend({
    
    /**
     * Forces to show label even it was not set by user. In this case it will be
     * shown as input name
     */
    showLabel: false,
    
    errorPopUp: false,
    
    /**
     * Event name after which internal value will be changed and validated
     */
    changeEvent: 'blur',
    
    ui: {
        input: 'input'
    },
    
    getUiValue: function() {
        return this.ui.input.val();
    },
    
    setUiValue: function(val) {
        this.ui.input.val(val);
    },
    
    getValue: function() {
        return this.model.get('value');
    },
    
    setValue: function(value) {
        this.setUiValue(value);
        this.model.set('value', value);
    },
    
    pickUpValue: function() {
        this.setValue(this.getUiValue());
    },
    
    onValueChange: function() {
        this.pickUpValue();
        this.validate();
    },
    
    reset: function() {
        this.setValue('');
    },
    
    validate: function() {
        this.model.validate();
        return this.model.isValid();
    },
    
    serializeData: function() {
        var serialized = Aes.ItemView.prototype.serializeData.apply(this, arguments);
        
        return _.extend(serialized, {
           view: this
        });
    },
    
    initialize: function() {
        
        if(!this.events)
            this.events = {};
        
        this.events[Marionette.getOption(this, 'changeEvent') + ' ' + this.ui.input] = 'onValueChange';

        if(!this.options.name)
            this.options.name = 'form-field-' + this.cid;

        if(Marionette.getOption(this, 'showLabel') && !this.options.label)
            this.options.label = this.options.name;

        if(this.options.validator)
            this.model = new (Backbone.Model.extend({
                validation: {
                    value: this.options.validator
                }
            }));
        else
            this.model = new Backbone.Model();
        
        Backbone.Validation.bind(this, {
            valid: function(view, attr) {
                view.$el.removeClass('invalid');
                view.$el.find('.error').remove();
                
                if(Marionette.getOption(view, 'errorPopUp') == true) {
                    view.ui.input.tooltip('destroy');
                }
            },
                    
            invalid: function(view, attr, error) {
                view.$el.find('.error').remove();
                view.$el.addClass('invalid');
                view.$el.append('<span class="help-block error">' + error + '</span>');
                
                if(Marionette.getOption(view, 'errorPopUp') == true) {
                    view.ui.input.tooltip({
                        title: error
                    });
                }
            }
        });
        
        Aes.ItemView.prototype.initialize.apply(this, arguments);
    }
}, {
    create: function(options) {
        var type, el;
        
        var opts = _.clone(options);
        
        type = options.type || 'text';
        
        opts = _.omit(opts, 'type', 'el');
        
        if(options.el)
        {
            var elType = typeof options.el;
            
            if(elType === 'string')
                el = $(options.el);
            else if(elType === 'function')
                el = options.el();
            else
                el = options.el;
            
            opts.el = el;
        }
        
        switch (type) 
        {
            case 'select':
                return new Aes.SelectFormField(opts);
            case 'radio':
                return new Aes.RadioFormField(opts);
            case 'radio-group':
                return new Aes.RadioGroupFormField(opts);
            default: 
                return new Aes.TextFormField(opts);
        }
    }
});

Aes.TextFormField = Aes.FormField.extend({
    
    inputType: 'text',
    
    labelInPlaceholder: false,
    
    getTplStr: function() {
        return Aes.TextFormField.getTpl();
    }
    
},{
    getTpl: function() {
        return '<% if(!view.options.labelInPlaceholder && view.options.label) { %><label for="<%= view.cid %>"><%= view.options.label %></label> <% } %>'
            + '<input ' 
                + 'id="<%= view.cid %>" ' 
                + 'type="<%= view.inputType %>" '
                + 'name="<%= view.options.name %>" '
                + '<% if(view.options.labelInPlaceholder) { %> placeholder="<%= view.options.label %>" <% } %>'
                + 'value="" '
            + '>';
    }
});

Aes.TextareaFormField = Aes.FormField.extend({
    
    getTplStr: function() {
        return Aes.TextareaFormField.getTpl();
    },

    ui: {
        input: 'textarea'
    }        
    
},{
    getTpl: function() {
        return '<% if(view.options.label) { %><label for="<%= view.cid %>"><%= view.options.label %></label> <% } %>'
            + '<textarea id="<%= view.cid %>" name="<%= view.options.name %>"></textarea>';
    }   
});

Aes.SelectFormField = Aes.FormField.extend({
    
    getTplStr: function() {
        return Aes.SelectFormField.getTpl();
    },    
    
    _options: false,
    
    ui: {
        input: 'select'
    },
    
    changeEvent: 'change',

    getUiValue: function() {
        return this.ui.input.children(':selected').val();
    },            
            
    getOptions: function() {
        return this._options;
    },
    /**
     * Picks up options from DOM
     */        
    pickUpOptions: function() {
        this._options = {};
        
        this.ui.input.children().each(_.bind(function(index, el) {
            var $el = $(el);
            this._options[$el.attr('value')] = $el.text();
        }, this));
        
        this.model.set('value', this.getUiValue());
    },        
            
    initialize: function() {
        Aes.FormField.prototype.initialize.apply(this, arguments);
        
        if(this.options.el)
            this.pickUpOptions();
        
        this._options = new Backbone.Collection(this.options.options);
    }
}, {
    getTpl: function() {
        return '<% if(view.options.label) { %><label for="<%= view.cid %>"><%= view.options.label %></label> <% } %>'
            + '<select name="<%= view.options.name %>">'
                + '<% _.each(view.getOptions().toJSON(), function(option) { %>'
                    + '<option value="<%= option.value %>" <% if(option.selected) { %>selected="selected"<% } %> ><%= ( option.label || option.value ) %></option>'
                +' <% }); %>'
            + '</select>';
    }
});

Aes.RadioFormField = Aes.FormField.extend({
    
    getTplStr: function() {
        return Aes.RadioFormField.getTpl();
    },
            
    getUiValue: function() {
        var result = false;
        
        if(this.ui.input.is(':checked'))
            result = this.ui.input.val();
        
        return result;
    },
    
    getValue: function() {
        return this.getUiValue();
    },

    check: function() {
        this.ui.input.prop('checked', true);
    },
            
    uncheck: function() {
        this.ui.input.attr('checked', false);
    },

    initialize: function(options) {
        Aes.FormField.prototype.initialize.apply(this, arguments);
        
        this.model.set('value', options.value || false)
        
        this.once('render', function() {
            this.setValue(this.model.get('value'));
            if(options.checked)
                this.check();
        });
    }
    
},{
    getTpl: function() {
        return '<label for="<%= view.cid %>" class="radio">'
            + '<input ' 
                + 'id="<%= view.cid %>" ' 
                + 'type="radio" '
                + 'name="<%= view.options.name %>" '
                + 'value="<%= view.options.value %>" '
            + '>'
            +'<%= view.options.label %>' 
        + '</label>';
    }
});

Aes.RadioGroupFormField = Aes.ItemView.extend({
    
    ui: {},
    
    attributes: function() {
        return {
            class: 'radios-group-form-field ' + this.options.name,
            id: this.cid
        };
    },
    
    getTplStr: function() {
        return Aes.RadioGroupFormField.getTpl();
    },    
    
    getUiValue: function() {
        return this.getValue();
    },
    
    setUiValue: function(val) {
        this.setValue(val);
    },
    
    getValue: function() {
        var checkedRadio = this.getChecked();
        
        if(checkedRadio)
            return checkedRadio.getValue();
        else
            return false;
    },
            
    getChecked: function() {
        return this.radios.find(function(radio) {
           if(radio.getValue() !== false)
               return true;
        });
    },
    
    setValue: function(value) {
        this.radios.each(function(radio) {
            if(value === '' || value === false || value === undefined)
                radio.uncheck();
            else if(value === radio.options.value)
                radio.check();
        });
    },
    
    pickUpValue: function() {
        this.model.set('value', this.getUiValue());
    },
    
    reset: function() {
        var options = this.options.options || [];
        var checkedOption = _.findWhere(options, {checked: true});
        var checkedVal = '';
        if(checkedOption)
            checkedVal = checkedOption.value;
        
        this.setValue(checkedVal);
    },
            
    /**
     * Note: Validation is not supported for the current moment by this type of field
     * @TODO: Implement validation
     */
    validate: function() {  
        
        return true;
        
//        this.model.validate();
//        return this.model.isValid();
    },

    render: function() {
        Aes.ItemView.prototype.render.apply(this, arguments);
        if(!this.radios.length)
            return;
        
        this.radios.each(function(radio){
            this.$el.append(radio.render().$el);
            radio.delegateEvents();
        }, this);
        
        return this;
    },
      
    onShow: function() {
        if(!this.radios.length)
            return;
        
        this.radios.each(function(radio) {
            radio.trigger('show');
        });
    },

    serializeData: function() {
        var serialized = Aes.ItemView.prototype.serializeData.apply(this, arguments);
        
        return _.extend(serialized, {
           view: this
        });
    },

    initialize: function(options) {
        Marionette.ItemView.prototype.initialize.apply(this, arguments);
        
        var radios = [];
        
        for(var i = 0; i < options.options.length; i++) {
            var radioConf = options.options[i];
            
            var radio = Aes.FormField.create(
                _.extend(
                    {}, 
                    radioConf, 
                    {
                       type: 'radio',
                       name: options.name
                    }
                )
            );
            radios.push(radio);
        }
        
        this.radios = new Backbone.ChildViewContainer(radios);
        
        this.model = new Backbone.Model();
    }    
}, {
    getTpl: function() {
        return '<% if(view.options.label) { %><span class="radios-heading"><%= view.options.label %></span><% } %>';
    }
});

Aes.FormView = Aes.ItemView.extend({
    
    showLabels: true,
    
    getTplStr: function() {
        return Aes.FormView.getTpl();
    },
    
    ui: {
        form: 'form'
    },
    
    submitBtnText: 'Submit',
    
    resetBtnText: 'Reset',
    
    _fields: null,
    
    events: {
        'click .form-submit': 'submit',
        'click .form-reset': 'reset'
    },
    
    onSubmit: function(event) {
        var handler = this.options.onSubmit;
        
        if(handler && typeof handler === 'function')
            return handler.apply(this, arguments);
    },
            
    onReset: function(event) {
        var handler = this.options.onReset;
        
        if(handler && typeof handler === 'function')
            return handler.apply(this, arguments);
    },

    _collectValues: function() {
        _.each(this._fields, function(field, fieldName){
            this.model.set(fieldName, field.getValue());
        }, this);
    },

    getValues: function() {
        this._collectValues();
        return this.model.toJSON();
    },
            
    hasErrors: function() {
        return false;
    },
            
    getErrors: function() {
        return {};
    },
            
    getField: function(fieldName) {
        return this._fields[fieldName];
    },
            
    validate: function() {

        var result = true;

        _.each(this._fields, function(field, fieldName){
            field.pickUpValue();
            
            if(!field.validate())
                result = false;
        }, this);
        
        return result;
    },
            
    submit: function(event) {
        event.preventDefault();
        if(this.validate())
            this.triggerMethod('submit', event);  
    },        
            
    reset: function(event) {

        _.each(this._fields, function(field) {
            field.reset();
        });
        
        this.triggerMethod('reset', event);
    },
    
    parseFieldConf: function(fieldName, fieldConf) {
        if(!fieldConf.name)
            fieldConf.name = fieldName;

        fieldConf.showLabel = Marionette.getOption(this, 'showLabels');

        if(this.options.uiAttributes && this.options.uiAttributes.inputs)
        {
            if(!fieldConf.uiAttributes) 
               fieldConf.uiAttributes = {};

            if(!fieldConf.uiAttributes.input)
                fieldConf.uiAttributes['input'] = this.options.uiAttributes.inputs;
        }
        
        return fieldConf;
    },
    
    setFields: function(fields) {
        this._fields = {};

        for(var fieldName in fields) {
            var fieldConf = this.parseFieldConf(fieldName, fields[fieldName]);
            
            this._fields[fieldName] = Aes.FormField.create(fieldConf);
        }
    },
            
    getFields: function() {
        return this._fields;
    },
            
    render: function() {
        Aes.ItemView.prototype.render.apply(this, arguments);
        if(!this._fields)
            return;
        
        var fieldNames = _.keys(this._fields);
        fieldNames.reverse();
        
        _.each(fieldNames, function(fieldName){
            var field = this._fields[fieldName];
            this.$('form').prepend(field.render().$el);
            field.delegateEvents();
        }, this);
        
        return this;
    },
      
    onShow: function() {
        if(!this._fields)
            return;
        
        _.each(this._fields, function(field) {
            field.trigger('show');
        });
    },
            
    serializeData: function() {
        return _.extend(Aes.ItemView.prototype.serializeData.apply(this, arguments), {
           submitBtnText: this.submitBtnText,
           resetBtnText: this.resetBtnText
        });
    },        
            
    initialize: function() {
        this.model = new Backbone.Model();
        this.setFields(this.options.fields);
        
        this.submitBtnText = Marionette.getOption(this, 'submitBtnText');
        this.resetBtnText = Marionette.getOption(this, 'resetBtnText');
    }
}, {
    getTpl: function() {
        return '<form class="form-vertical well"><div class="form-actions">'
            + '<input type="button" class="btn btn-primary form-submit" value="<%= submitBtnText %>">'
            + '&nbsp;<input type="button" class="btn form-reset" value="<%= resetBtnText %>">'
        + '</div></form>';
    }
});

Aes.NavbarFormView = Aes.FormView.extend({
    getTplStr: function() {
        return Aes.NavbarFormView.getTpl();
    },
            
    parseFieldConf: function(fieldName, fieldConf) {
        fieldConf = Aes.FormView.prototype.parseFieldConf.apply(this, arguments);
        fieldConf.labelInPlaceholder = true;
        fieldConf.errorPopUp = true;
        return fieldConf;
    }
},{
    getTpl: function() {
        return '<form class="form-inline navbar-search"><div class="btn-group">'
            + '<input type="button" class="btn form-submit" value="<%= submitBtnText %>">'
            + '&nbsp;<input type="button" class="btn form-reset" value="<%= resetBtnText %>">'
        + '</div></form>';
    }
});