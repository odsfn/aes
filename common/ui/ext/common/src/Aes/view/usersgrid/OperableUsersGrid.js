// Lists all users in the system, provides customizable operation for set of
// selected users
Ext.define('Aes.view.usersgrid.OperableUsersGrid', {
    requires: [
        'Aes.view.usersgrid.UsersGrid'
    ],
    extend: 'Ext.panel.Panel',
    xtype: 'aes-operableusersgrid',
    layout: 'fit',
    items: {
        xclass: 'Aes.view.usersgrid.UsersGrid',
        border: false,
        selType: 'checkboxmodel',
        selModel: {
            checkOnly: true
        }
    },
    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'bottom',
            layout: { pack: 'end' },
            items: [
                {
                    id: 'add-users-btn',
                    itemId: 'addBtn',
                    scale: 'large',
                    text: '<b>Add</b>',
                    handler: 'onAddBtnClicked',
                    disabled: true
                }
            ]
        }
    ],
    
    initItems: function() {
        if (this.storeScopes) {
            this.items.storeScopes = this.storeScopes;
        }
        
        this.callParent();
    }
});