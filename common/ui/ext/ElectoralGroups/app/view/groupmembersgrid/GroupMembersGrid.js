// Lists all users in the system, allows to extend it to provide 
// custom operations
Ext.define('ElectoralGroups.view.groupmembersgrid.GroupMembersGrid', {
    extend: 'Aes.view.usersgrid.UsersGrid',
    xtype: 'groupmembersgrid',
    controller: 'groupmembersgrid',
    selModel: Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    }),
    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    text:'Add',
                    handler: 'onClickAddButton'
                }, {
                    itemId: 'removeButton',
                    text:'Remove',
                    disabled: true
                }
            ]
        },
        {
            dock: 'bottom',
            xtype: 'pagingtoolbar',
            pageSize: 10,
            displayInfo: true
        }
    ]
});