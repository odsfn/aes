/* 
 * This Grid represents users which has applied requests to became electors
 */
Ext.define('ElectoralGroups.view.requestsgrid.RequestsGrid', {
    requires: [
        'ElectoralGroups.view.requestsgrid.RequestsGridController'
    ],
    extend: 'Aes.view.usersgrid.UsersGrid',
    xtype: 'requestsgrid',
    controller: 'requestsgrid',
    selType: 'checkboxmodel',
    selModel: {
        checkOnly: true
    },
    columns: {
        items: [
            {   
                text: 'Request ID', dataIndex: 'electorRegistrationRequest', flex: 1, 
                filter: false,
                renderer: function(value, metaData, record) {
                    return value.id;
                }
            },
            {   
                text: 'User ID', dataIndex: 'user_id', flex: 1, filter: 'number'
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
                    options: ['Male', 'Female', '-']
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
            },
            {
                xtype: 'actioncolumn',
                flex: 2,
                text: 'Actions',
                items: [
                    {
                        icon: '/ui/ext/resources/fontawesome/check.png',
                        tooltip: 'Accept',
                        handler: 'onAcceptClick'
                    }, '-', 
                    {
                        icon: '/ui/ext/resources/fontawesome/ban.png',
                        tooltip: 'Decline',
                        handler: 'onDeclineClick'
                    }
                ],
                hidden: true
            }
        ]
    },    
    
    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
                {
                    xtype: 'box',
                    html: 'Operations on selected set:'
                },
                {
                    itemId: 'confirmManyBtn',
                    text:'Accept',
                    disabled: true,
                    handler: 'onClickAcceptMany'
                }, {
                    itemId: 'declineManyBtn',
                    text:'Decline',
                    disabled: true,
                    handler: 'onClickDeclineMany'
                }
            ]
        },
        {
            dock: 'bottom',
            xtype: 'pagingtoolbar',
            pageSize: 25,
            displayInfo: true
        }
    ],
    
    initStore: function() {
        this.storeScopes = {
            withElectorRegistrationRequest: {
                election_id: ElectoralGroups.app.options.electionId,
                status: 0
            }
        };
        
        this.callParent();
    }
});

