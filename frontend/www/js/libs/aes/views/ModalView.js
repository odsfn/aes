/* 
 * Modal view
 */
var Aes = Aes || {};

/**
 * Base class for modals
 * 
 * @type @exp;Aes@pro;ItemView@call;extend
 */
Aes.ModalView = Aes.ItemView.extend({
    
    label: '',
    
    body: '',
    
    closeBtnLabel: 'Close',
    
    ui: {
        footer: 'div.modal-footer'
    },
    
    triggers: {
        'click': 'closeClicked'
    },
    
    /**
     * @returns {Array} Configured buttons objects
     */
    buttonsConfig: function() {
        return [
            new Aes.ButtonView({
                label: Marionette.getOption(this, 'closeBtnLabel'),
                onClick: _.bind(function() {
                    this.triggerMethod('closeClicked');
                }, this)
            })         
        ];
    },
    
    getTplStr: function() {
        return Aes.ModalView.getTpl();
    },
    
    open: function() {
        if(!this._isRendered)
            this.render();
        
        if(!this._isShown)
            $('body').append(this.$el);
        
        this.$el.find('.modal').modal('show');
    },
    
    onRender: function() {
        _.each(this._buttons, function(btn) {
            this.ui.footer.append(btn.render().$el);
        }, this);
    },
    
    onClose: function() {
        Marionette.getOption(this, 'onCancel').call(this);
        this.$el.find('.modal').modal('hide');
    },
    
    onCloseClicked: function() {
        this.close();
    },
    
    //should be overriden or passed as constructor options
    onCancel: function() {},
    
    serializeData: function() {
        return _.extend(Aes.ItemView.prototype.serializeData.apply(this, arguments),{
            view: {
                attributes: this.attributes,
                cid: this.cid
            }
        });
    },    
    
    initialize: function() {        
        Aes.ItemView.prototype.initialize.apply(this, arguments);
        
        this.model = new Backbone.Model({
            label: Marionette.getOption(this, 'label'),
            body: Marionette.getOption(this, 'body')
        });
        
        var btnsConfigProvider = Marionette.getOption(this, 'buttonsConfig');
        
        this._buttons = btnsConfigProvider.call(this);
    }
    
},{
    getTpl: function() {
        return '<div id="<%= view.cid %>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="<%= view.cid %>-label" aria-hidden="true">'
                    + '<div class="modal-header">'
                        + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
                        + '<h3 id="<%= view.cid %>-label"><%= label %></h3>'
                    + '</div>'
                    + '<div class="modal-body"><%= body %></div>'
                    + '<div class="modal-footer"></div>'
                + '</div>';
    }
});

Aes.ConfirmModalView = Aes.ModalView.extend({
    label: 'Confirmation',
    
    body: 'Are you sure?',
    
    closeBtnLabel: 'No',
    
    confirmBtnLabel: 'Yes',
    
    onConfirmClicked: function() {
        Marionette.getOption(this, 'onConfirm').call(this);
        this.close();
    },
    
    //should be overriden or passed as constructor options
    onConfirm: function() {},
    
    buttonsConfig: function() {
        return [
            new Aes.ButtonView({
                label: Marionette.getOption(this, 'closeBtnLabel'),
                onClick: _.bind(function() {
                    this.triggerMethod('closeClicked');
                }, this)
            }),
            new Aes.ButtonView({
                attributes: {
                    class: 'btn btn-primary'
                },
                label: Marionette.getOption(this, 'confirmBtnLabel'),
                onClick: _.bind(function() {
                    this.triggerMethod('confirmClicked');
                }, this)
            })
        ];
    }
});

Aes.AlertModalView = Aes.ModalView.extend({
    label: 'Alert'
});

