Ext.define('Aes.view.groupsgrid.GroupsGridController', {
    extend: 'Ext.app.ViewController',

    requires: [
        'Ext.MessageBox',
        'Aes.store.VoterGroupMembers',
        'Aes.view.groupmembersgrid.GroupMembersGrid'
    ],

    alias: 'controller.groupsgrid',

    createModel: function() 
    {
        var model = new Aes.model.VoterGroup();
        model.set('id', null);
        model.set('election_id', null);
        model.set('user_id', this.view.getViewModel().userId);
        model.set('type', Aes.model.VoterGroup.getTypeId('Global'));
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
    },
    
    onCopyClick: function(view, rowIndex, colIndex, item, e, record, row)
    {
        var me = this,
            grid = this.view,
            mainView = grid.up('app-main');
    
        mainView.mask();
        Ext.Ajax.request({
            url: Aes.UrlHelper.getBaseUrl() + 'admin/copyGroup',
            params: {
                id: record.get('id')
            },
            success: function(response) {
                var respData = Ext.decode(response.responseText),
                    title = 'Operation finished successfully',
                    icon = null,
                    message = respData.message;
                
                if (!respData.success) {
                    title = 'Operation failed';
                    icon = Ext.Msg.ERROR;
                }
                
                Ext.Msg.show({
                    title: title,
                    message: message,
                    icon: icon
                });

                grid.getStore().reload();
                mainView.unmask();
            },
            failure: function(response) {
                Ext.Msg.show({
                    title: "Operation failed",
                    message: response.responseText,
                    icon: Ext.Msg.ERROR
                });
                mainView.unmask();
            }
        });
    }
});

