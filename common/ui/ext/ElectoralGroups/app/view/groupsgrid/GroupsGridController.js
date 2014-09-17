Ext.define('ElectoralGroups.view.groupsgrid.GroupsGridController', {
    extend: 'Ext.app.ViewController',

    requires: [
        'Ext.MessageBox',
        'ElectoralGroups.store.VoterGroupMembers',
        'ElectoralGroups.view.groupmembersgrid.GroupMembersGrid'
    ],

    alias: 'controller.groupsgrid',

    createModel: function() 
    {
        var model = new ElectoralGroups.model.VoterGroup();
        model.set('id', null);
        model.set('user_id', ElectoralGroups.app.options.userId);
        model.set('election_id', ElectoralGroups.app.options.electionId);
        model.set('type', ElectoralGroups.model.VoterGroup.getTypeId('Local'));
        model.set('name', '');
        return model;
    },

    onClickAddButton: function () 
    {
        var rowEditing = this.view.findPlugin('rowediting');
        rowEditing.cancelEdit();
        
        var store = this.view.getStore();
        var newGroup = this.createModel();
        store.insert(store.count(), newGroup);
        rowEditing.startEdit(newGroup);
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
    },
    
    onAssignedChange: function(checkCell, index, checked) 
    {
        var model = this.view.getStore().getAt(index);
        var assignments = ElectoralGroups.app.getStore('ElectionVoterGroups');
        
        if(checked)
            this.onAssign(model, assignments);
        else
            this.onUnassign(model, assignments);
    },
    
    onAssign: function(group, assignments) 
    {
        var m = Ext.create('ElectoralGroups.model.ElectionVoterGroup');
        m.set({
            id: null,
            election_id: ElectoralGroups.app.options.electionId,
            voter_group_id: group.get('id')
        });
        
        assignments.add(m);
    },
    
    onUnassign: function(group, assignments) 
    {
        var assignment = assignments.findRecord('voter_group_id', group.get('id'));
        
        if(assignment)
            assignments.remove(assignment);
    },
    
    onOpenClick: function(view, rowIndex, colIndex, item, e, record, row) 
    {
        var tabPanel = Ext.ComponentQuery.query('app-main > tabpanel')[0],
            tab = Ext.ComponentQuery.query('#usersgrid-group-' + record.get('id'))[0];
        
        if(!tab) {
            var users = Ext.create('Aes.store.Users', {
                filters: [
                    { 
                        property: 'applyScopes', 
                        value: Ext.encode({
                            inVoterGroup: {
                                voter_group_id: record.get('id')
                            }
                        }) 
                    }
                ],
                remoteFilter: true,
                autoLoad: true
            });
            
            tab = tabPanel.add({
                itemId: 'usersgrid-group-' + record.get('id'),
                title: record.get('name'),
                xtype: 'groupmembersgrid',
                store: users,
                viewModel: {
                    data: {
                        group: record
                    }
                },
                closable: true
            });
        }
        
        tabPanel.setActiveTab(tab);
    },
    
    onEditClick: function(view, rowIndex, colIndex, item, e, record, row)
    {
        var rowEditing = this.view.findPlugin('rowediting');
        rowEditing.cancelEdit();
        
        rowEditing.startEdit(record);
    }
});

