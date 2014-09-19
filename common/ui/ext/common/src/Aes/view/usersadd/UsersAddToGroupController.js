Ext.define('Aes.view.usersadd.UsersAddToGroupController', {
    requires: [
        'Aes.model.VoterGroupMember'
    ],
    
    extend: 'Ext.app.ViewController',

    alias: 'controller.add-to-group',

    onAddBtnClicked: function() 
    {
        var window = this.getView().up('window'),
            grid = this.getView().down('grid'),
            sel = grid.getSelectionModel(),
            group = this.getView().up().getViewModel().data.group,
            members = this.getView().up().getViewModel().data.members;
            
            done = function() {
                grid.getStore().reload();
                window.unmask();
            };
   
        window.mask();

        var groupMembers = [];
        Ext.each(sel.getSelection(), function(user, index) {
            var newMember = Ext.create('Aes.model.VoterGroupMember', {
                voter_group_id: group.get('id'),
                user_id: user.get('user_id')
            });
            groupMembers.push(newMember);
        });
        
        members.add(groupMembers);
        members.sync({
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

