Ext.define('Aes.view.groupsgrid.GroupsGrid', {
    extend: 'Ext.grid.Panel',
    requires: [
        'Ext.data.validator.Length',
        'Ext.grid.column.Check'
    ],
    xtype: 'groupsgrid',
    selType: 'checkboxmodel',
    selModel: {
        checkOnly: true
    },
    controller: 'groupsgrid',
    plugins: [
        {
            ptype: 'gridfilters'
        },
        {
            ptype: 'rowediting',
            clicksToEdit: 2,
            listeners: {
                cancelEdit: function(rowEditing, context) {
                    // Canceling editing of a locally added, unsaved record: remove it
                    if (context.record.phantom) {
                        context.grid.getStore().remove(context.record);
                    }
                }
            }
        }
    ],
    columns: {
        defaults: {
            width  : 120
        },
        items: [
            {   
                text: 'ID', dataIndex: 'id', flex: 1, filter: 'number',
                renderer: function(v, meta, rec) {
                    return rec.phantom ? '' : v;
                } 
            },
            { 
                text: 'Name', dataIndex: 'name',
                flex: 6, 
                filter: {
                    type: 'string',
                    itemDefaults: {
                        emptyText: 'Search for...'
                    }
                },
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
            { 
                text: 'Type', dataIndex: 'type', flex: 2,
                renderer: function(value, metaData, record) {
                    return record.get('typeLabel');
                },
                filter: {
                    type: 'list',
                    options: [
                        [0, 'Global'],
                        [1, 'Local']
                    ]
                },
                editor: false
            },
            {
                text: 'Election ID', dataIndex: 'election_id', flex: 2,
                filter: 'number', editor: false
            },
            {
                xtype: 'actioncolumn',
                flex: 3,
                text: 'Actions',
                iconCls: ' fontawesome-icon',
                items: [
                    {
                        icon: '/ui/ext/resources/fontawesome/list.png',
                        tooltip: 'Opens tab with users grid for this group',
                        handler: 'onOpenClick'
                    },
                    {
                        icon: '/ui/ext/resources/fontawesome/edit.png',
                        tooltip: 'Edit this group',
                        handler: 'onEditClick'
                    },
                    {
                        icon: '/ui/ext/resources/fontawesome/copy.png',
                        tooltip: 'Make global copy of local group',
                        handler: 'onCopyClick',
                        isDisabled: function(view, rowIndex, colIndex, item, record) {
                            return record.get('typeLabel') == 'Global';
                        }
                    }
                ]
            }
        ]
    },
    dockedItems: [{
        xtype: 'toolbar',
        dock: 'top',
        items: [{
            id: 'create-group-btn',
            text:'Add Group',
            tooltip:'Add a new group',
            handler: 'onClickAddButton'
        }, {
            id: 'remove-groups-btn',
            itemId: 'removeButton',
            text:'Remove Groups',
            tooltip:'Remove the selected group',
            handler: 'onClickRemoveButton',
            disabled: true
        }]
    }, {
        dock: 'bottom',
        xtype: 'pagingtoolbar',
        pageSize: 25,
        displayInfo: true
    }],

    _storeName: 'Aes.store.VoterGroups',

    initComponent: function() {
        this.callParent(arguments);
        
        var store = Ext.getStore(this._storeName);
        
        this.setStore(store);
        this.query('pagingtoolbar')[0].setStore(this.getStore());
        
        var grid = this;
        this.getSelectionModel().on('selectionchange', function(selModel, selections){
            grid.down('#removeButton').setDisabled(selections.length === 0);
        });
        
        store.load();
    }
});