// Lists all electors in current election
Ext.define('ElectoralGroups.view.electorsgrid.ElectorsGrid', {
    extend: 'Aes.view.usersgrid.UsersGrid',
    xtype: 'electorsgrid',
    selModel: Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    }),
    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    text:'Add'
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