Ext.define('ElectoralGroups.view.groupmembersgrid.GroupMembersGridController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.groupmembersgrid',

    onClickAddButton: function () 
    {
        var usersadd = Ext.create('ElectoralGroups.view.usersadd.UsersAdd', {
            viewModel: {
                data: {
                    group: this.getView().getViewModel().getData().group
                }
            }
        });
        usersadd.show();
    },

    onClickRemoveButton: function() 
    {
        var store = this.view.getStore();
        var selections = this.view.getView().getSelectionModel().getSelection();
        
        Ext.Msg.confirm('Confirm', 'You are going to remove ' + selections.length + ' records. Are you sure?', function(choice) {
            if (choice === 'yes') {
                store.remove(selections);
            }
        }, this);
    }
});

