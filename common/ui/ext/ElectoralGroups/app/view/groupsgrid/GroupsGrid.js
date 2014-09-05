Ext.define('ElectoralGroups.view.groupsgrid.GroupsGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'groupsgrid',
    selModel: Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    }),
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
                text: 'Type', dataIndex: 'type', flex: 3, 
                filter: {
                    type: 'list'
                }
            },
            {
                xtype: 'checkcolumn',
                text: 'Is Assigned', dataIndex: 'assigned', flex: 3,
                filter: 'boolean',
                listeners: {
                    checkchange: 'onAssignedChange'
                },
                editor: false
            },
            {
                xtype: 'actioncolumn',
                flex: 2,
                text: 'Actions',
                items: [
                    {
                        icon: '/ui/ext/resources/fontawesome/list.png',
                        tooltip: 'Opens tab with users grid for this group',
                        handler: 'onOpenClick'
                    }, '-', 
                    {
                        icon: '/ui/ext/resources/fontawesome/edit.png',
                        tooltip: 'Edit this group',
                        handler: 'onEditClick'
                    }
                ]
            }
        ]
    },
    dockedItems: [{
        xtype: 'toolbar',
        dock: 'top',
        items: [{
            text:'Add Group',
            tooltip:'Add a new group',
            handler: 'onClickAddButton'
        }, {
            itemId: 'removeButton',
            text:'Remove Groups',
            tooltip:'Remove the selected group',
            handler: 'onClickRemoveButton',
            disabled: true
        }]
    }, {
        dock: 'bottom',
        xtype: 'pagingtoolbar',
        pageSize: 100,
        store: Ext.getStore('VoterGroups'),
        displayInfo: true
    }],
    initComponent: function(config) {
        this.callParent(arguments);
        this.setStore(Ext.getStore('VoterGroups'));
        this.query('pagingtoolbar')[0].setStore(this.getStore());
        
        var grid = this;
        this.getSelectionModel().on('selectionchange', function(selModel, selections){
            grid.down('#removeButton').setDisabled(selections.length === 0);
        });
    }
});