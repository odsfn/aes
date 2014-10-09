Ext.define('ElectoralGroups.view.groupsgrid.GroupsGrid', {

    extend: 'Aes.view.groupsgrid.GroupsGrid',
    
    xtype: 'groupsgrid',
    
    _storeName: 'AssignableVoterGroups',
    
    initComponent: function()
    {
        this.columns.items.splice(3, 1, {
            xtype: 'checkcolumn',
            text: 'Is Assigned', dataIndex: 'assigned', flex: 3,
            filter: 'boolean',
            listeners: {
                checkchange: 'onAssignedChange'
            },
            editor: false
        });
        
        this.columns.items[4].flex = 2;
        
        var actions = this.columns.items[4].items;
        actions[1].isDisabled = function(view, rowIndex, colIndex, item, record) {
            return record.get('typeLabel') == 'Global';
        };
        
        actions.splice(2, 1);
        
        this.callParent(arguments);
    }
});