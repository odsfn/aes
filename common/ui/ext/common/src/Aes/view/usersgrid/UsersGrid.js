// Lists all users in the system
Ext.define('Aes.view.usersgrid.UsersGrid', {
    extend: 'Ext.grid.Panel',
    requires: [
        'Aes.store.Users'
    ],
    xtype: 'usersgrid',
    plugins: [
        {
            //@TODO: Render filters to the header of grid or to the sidebar
            ptype: 'gridfilters'
        }
    ],

    columns: {
        items: [
            {   
                text: 'ID', dataIndex: 'user_id', flex: 1, filter: 'number'
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
                    dateWriteFormat: "Y-m-d"
                } 
            }
        ]
    },
    dockedItems: [
        {
            dock: 'bottom',
            xtype: 'pagingtoolbar',
            pageSize: 25,
            displayInfo: true
        }
    ],
    
    initComponent: function() {
        this.callParent(arguments);
        
        this.initStore();
        
        this.query('pagingtoolbar')[0].setStore(this.getStore());
        var store = this.getStore();
        
        store.load();
    },
    
    initStore: function() {
        var conf = {};
        
        if(!this.getInitialConfig('store') 
            && Ext.getClassName(this.getStore()) === 'Ext.data.Store')
        {
            if (this.storeScopes) {
                conf = {
                    filters: [
                        { 
                            property: 'applyScopes', 
                            value: Ext.encode(this.storeScopes) 
                        }
                    ],
                    remoteFilter: true
                };
            }
            
            this.setStore(Ext.create('Aes.store.Users', conf));
        }
    }
});