Ext.define('ElectoralGroups.view.groupmembersgrid.GroupMembersGridController', {
    requires: [
        'ElectoralGroups.view.usersadd.UsersAdd'
    ],
    extend: 'Ext.app.ViewController',

    alias: 'controller.groupmembersgrid',

    onClickAddButton: function () 
    {
        var group = this.group,
            usersadd = Ext.create('ElectoralGroups.view.usersadd.UsersAdd', {
                viewModel: {
                    data: {
                        group: group,
                        members: this.members
                    }
                },
                
                storeScopes: { 
                    notInVoterGroup: {
                        voter_group_id: group.get('id')
                    }
                }
            });
        usersadd.show();
        
        usersadd.on('close', function() {
            this.view.getStore().reload();
        }, this);
    },

    onClickRemoveButton: function() 
    {
        var selections = this.view.getView().getSelectionModel().getSelection();
        var models = [];
        
        Ext.Msg.confirm('Confirm', 'You are going to remove ' + selections.length + ' records. Are you sure?', function(choice) {
            if (choice === 'yes') {
                Ext.each(selections, function(sel) {
                    models.push(
                        this.members.getById(
                            parseInt(sel.get('voterGroupMember').id)
                        )
                    );
                }, this);
                
                this.members.remove(models);
                this.members.sync({ 
                    success: function(){
                        this.view.getStore().reload();
                    },
                    scope: this
                });
            }
        }, this);
    },
    
    init: function() 
    {
        this.group = this.getView().getViewModel().getData().group;
        this.members = Ext.create('ElectoralGroups.store.VoterGroupMembers', {
            filters: [
                { property: 'voter_group_id', value: this.group.get('id') }
            ],
            autoSync: false
        });
        this.members.load();
        
        var grid = this.view;
        this.view.getSelectionModel().on('selectionchange', function(selModel, selections){
            grid.down('#removeButton').setDisabled(selections.length === 0);
        });
    }
});

