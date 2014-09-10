// Lists all electors in current election
Ext.define('ElectoralGroups.view.electorsgrid.ElectorsGrid', {
    extend: 'Aes.view.usersgrid.UsersGrid',
    xtype: 'electorsgrid',
    controller: 'electorsgrid',
    selModel: Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    }),
    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    itemId: 'addButton',
                    text:'Add',
                    disabled: false,
                    handler: 'onClickAddButton'
                }, {
                    itemId: 'removeButton',
                    text:'Remove',
                    disabled: true,
                    handler: 'onClickRemoveButton'
                }, '-', {
                    itemId: 'registerButton',
                    text: 'Register electors from groups',
                    disabled: true,
                    handler: 'onClickRegisterButton'
                }
            ]
        },
        {
            dock: 'bottom',
            xtype: 'pagingtoolbar',
            pageSize: 25,
            displayInfo: true
        }
    ]
});