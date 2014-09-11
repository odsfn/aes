// Lists all users in the system, allows to extend it to provide 
// custom operations
Ext.define('ElectoralGroups.view.groupmembersgrid.GroupMembersGrid', {
    requires: [
        'ElectoralGroups.view.groupmembersgrid.GroupMembersGridController'
    ],
    extend: 'Aes.view.usersgrid.UsersGrid',
    xtype: 'groupmembersgrid',
    controller: 'groupmembersgrid',
    
    columns: {
        items: [
            {   
                text: 'User Id', dataIndex: 'user_id', flex: 1, filter: 'number'
            },
            { 
                text: 'First Name', dataIndex: 'first_name', flex: 1, 
                filter: {
                    type: 'string',
                    itemDefaults: {
                        emptyText: 'Search for...'
                    }
                }
            },
            { 
                text: 'Last Name', dataIndex: 'last_name', flex: 1,
                filter: {
                    type: 'string',
                    itemDefaults: {
                        emptyText: 'Search for...'
                    }
                }
            },
            { 
                text: 'Email', dataIndex: 'email', flex: 1,
                filter: {
                    type: 'string',
                    itemDefaults: {
                        emptyText: 'Search for...'
                    }
                }
            },
            { 
                text: 'Gender', dataIndex: 'gender', flex: 1,
                filter: {
                    type: 'list',
                    options: [
                        [1, 'Male'], 
                        [2, 'Female'], 
                        [0, 'Not set']
                    ]
                }
            },
            { 
                text: 'Birth Place', dataIndex: 'birth_place', flex: 1,
                filter: {
                    type: 'string',
                    itemDefaults: {
                        emptyText: 'Search for...'
                    }
                } 
            },
            { 
                text: 'Birth Date', dataIndex: 'birth_day', xtype: 'datecolumn', 
                format: 'd.m.Y', flex: 1,
                filter: {
                    // @TODO: fix on selection that ( it hides when I try to
                    // select year less than 2000
                    type: 'date',
                    dateFormat: 'd.m.Y'
                } 
            }
        ]
    },    
    
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
                    handler: 'onClickRemoveButton',
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