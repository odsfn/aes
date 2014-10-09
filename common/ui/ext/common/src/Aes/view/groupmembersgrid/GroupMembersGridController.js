Ext.define('Aes.view.groupmembersgrid.GroupMembersGridController', {
    requires: [
        'Aes.view.usersadd.UsersAdd',
        'Aes.view.usersadd.UsersAddToGroupController'
    ],
    extend: 'Ext.app.ViewController',

    alias: 'controller.groupmembersgrid',

    onClickAddButton: function () 
    {
        var group = this.group,
            usersadd = Ext.create('Aes.view.usersadd.UsersAdd', {
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
        this.members = Ext.create('Aes.store.VoterGroupMembers', {
            filters: [
                { property: 'voter_group_id', value: this.group.get('id') }
            ],
            remoteFilter: true,
            autoSync: false,
            autoLoad: false
        });
        
        var grid = this.view,
            loadRelatedMembers = function(users, records, success, eOpts) {
                var memberIds = [];
                
                Ext.each(records, function(record) {
                    memberIds.push(record.get('voterGroupMember').id);
                });
                
                if(memberIds.length === 0) return;
                
                this.view.mask();
                this.members.setFilters([
                    { property: 'voter_group_id', value: this.group.get('id') },
                    { property: 'id', operation: 'in', value: memberIds}
                ]);
                this.members.load({
                    scope: this,
                    callback: function(records, operation, success) {
                        if(!success) throw new Error("Failed to loadRelatedElectors");
                        this.view.unmask();
                    }
                });
            };
        
        grid.getStore().on('load', loadRelatedMembers, this);
        
        this.view.getSelectionModel().on('selectionchange', function(selModel, selections){
            grid.down('#removeButton').setDisabled(selections.length === 0);
        });
    }
});

