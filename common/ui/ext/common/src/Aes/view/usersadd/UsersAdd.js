// Window to select users and add them to selected group
Ext.define('Aes.view.usersadd.UsersAdd', {
    requires: [
        'Aes.view.usersgrid.OperableUsersGrid',
        'Aes.view.usersadd.UsersAddToGroupController'
    ],
    extend: 'Ext.window.Window',
    xtype: 'usersadd',
    
    id: 'add-electors-window',
    title: 'Users adding to the selected group',
    maximized: true,
    resizable: false,
    draggable: false,
    layout: 'fit',
    
    items: {
        xclass: 'Aes.view.usersgrid.OperableUsersGrid',
        controller: 'add-to-group',
        border: false
    },
    
    initItems: function() {
        if (this.storeScopes) {
            this.items.storeScopes = this.storeScopes;
        }
        
        this.callParent();
    }
});