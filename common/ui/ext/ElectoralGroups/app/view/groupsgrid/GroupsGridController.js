Ext.define('ElectoralGroups.view.groupsgrid.GroupsGridController', {
    extend: 'Ext.app.ViewController',

    requires: [
        'Ext.MessageBox',
        'Aes.store.VoterGroupMembers',
        'Aes.view.groupmembersgrid.GroupMembersGrid',
        'ElectoralGroups.view.groupmembersgrid.GroupMembersGridController'
    ],

    alias: 'controller.groupsgrid',

    createModel: function() 
    {
        var model = new ElectoralGroups.model.AssignableVoterGroup();
        model.set('id', null);
        model.set('user_id', ElectoralGroups.app.options.userId);
        model.set('election_id', ElectoralGroups.app.options.electionId);
        model.set('type', ElectoralGroups.model.AssignableVoterGroup.getTypeId('Local'));
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
        var globalGroups = [];
        
        Ext.each(selections, function(group, index, selectionsItSelf) {
            if(group.get('typeLabel') == 'Global')
                globalGroups.push(group);
        }, this);
        
        var message = 'You are going to remove ' + selections.length + ' records. Are you sure?';
        
        if (globalGroups.length) {
            message += '<br> Please note, you are also selected ' + globalGroups.length
                + ' Global groups. Global groups will not be deleted.';
        
            Ext.each(globalGroups, function(group) {
                selections.splice(selections.indexOf(group), 1);
            });
        }
        
        Ext.Msg.confirm('Confirm', 
            message, 
            function(choice) {
                if (choice === 'yes') {                   
                    store.remove(selections);
                }
            }, 
            this
        );
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

