// Lists all users in the system, provides customizable operation for set of
// selected users
Ext.define('Aes.view.usersgrid.OperableUsersGrid', {
    extend: 'Ext.panel.Panel',
    xtype: 'aes-operableusersgrid',
    layout: 'fit',
    items: {
        xclass: 'Aes.view.usersgrid.UsersGrid',
        border: false,
        selModel: Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true
        })
    },
    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'bottom',
            layout: { pack: 'end' },
            items: [
                {
                    itemId: 'addBtn',
                    scale: 'large',
                    text: '<b>Add</b>',
                    handler: 'onAddBtnClicked',
                    disabled: true
                }
            ]
        }
    ],
    initComponent: function() {
        this.callParent(arguments);
    }
});