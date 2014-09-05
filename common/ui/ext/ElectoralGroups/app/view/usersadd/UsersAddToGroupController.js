Ext.define('ElectoralGroups.view.usersadd.UsersAddToGroupController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.add-to-group',

    onAddBtnClicked: function() 
    {
        var window = this.getView().up('window'),
            grid = this.getView().down('grid'),
            sel = grid.getSelectionModel(),
            group = this.getView().up().getViewModel().data.group,
            
            done = function() {
                
                // @TODO: Update grid instead of rows removal after
                // filters will be implemented on backend
                Ext.each(sel.getSelection(), function(m, i) {
                    var rowEl = grid.getView().getRow(m);
                    var node = Ext.get(rowEl);
                    node.destroy();
                });
                sel.deselect(sel.getSelection());
                window.unmask();
            };
   
        window.mask();
        
        console.log('Add clicked. @TODO: Implement real adding');

        var groupMembers = [];
        Ext.each(sel.getSelection(), function(user, index) {
            var newMember = Ext.create('ElectoralGroups.model.VoterGroupMember', {
                voter_group_id: group.get('id'),
                user_id: user.get('user_id')
            });
            groupMembers.push(newMember);
        });
        
        var store = Ext.create('ElectoralGroups.store.VoterGroupMembers', {
            autoSync: false
        });
        
        store.add(groupMembers);
        store.sync({
            success: done,
            failure: function() {
                console.error('Sync failed');
            }
        });
    },
    
    onSelectionChange: function(selModel, selections)
    {
        this.getView().down('#addBtn').setDisabled(selections.length === 0);
    },
    
    init: function(view)
    {
        view.down('grid').getSelectionModel().on('selectionchange', this.onSelectionChange, this);
    }
});

