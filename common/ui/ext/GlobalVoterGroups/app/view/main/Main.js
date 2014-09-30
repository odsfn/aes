Ext.define('GlobalVoterGroups.view.main.Main', {
    requires: [
        'Aes.view.groupsgrid.GroupsGrid'
    ], 
    
    extend: 'Ext.container.Container',

    xtype: 'app-main',
    
    controller: 'main',
    viewModel: {
        type: 'main'
    },

    layout: {
        type: 'border'
    },

    items: [{
        xtype: 'groupsgrid',
        bind: {
            title: '{name}'
        },
        region: 'west',
        flex: 2,
        split: true,
        collapsible: true
    },{
        region: 'center',
        xtype: 'tabpanel',
        title: 'Members in group',
        flex: 4
    }],

    initItems: function() {
        this.items[0].viewModel = {
            userId: GlobalVoterGroups.app.options.userId
        };
        
        this.callParent();
    }
});
