// Lists all electors in current election
Ext.define('ElectoralGroups.view.electorsgrid.ElectorsGrid', {
    requires: [
        'ElectoralGroups.model.Elector'
    ],
    extend: 'Aes.view.usersgrid.UsersGrid',
    xtype: 'electorsgrid',
    controller: 'electorsgrid',
    selType: 'checkboxmodel',
    selModel: {
        checkOnly: true
    },
    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    id: 'add-elector-btn',
                    itemId: 'addButton',
                    text:'Add',
                    disabled: false,
                    handler: 'onClickAddButton'
                }, {
                    id: 'remove-elector-btn',
                    itemId: 'removeButton',
                    text:'Remove',
                    disabled: true,
                    handler: 'onClickRemoveButton'
                }, '-', {
                    id: 'register-electors-btn',
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
    ],
    
    initStore: function() {
        this.storeScopes = {
            elector: {
                election_id: ElectoralGroups.app.options.electionId,
                status: ElectoralGroups.model.Elector.STATUS_ACTIVE
            }
        };
        
        this.callParent();
    }
});