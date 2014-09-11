Ext.define('ElectoralGroups.view.electorsgrid.ElectorsGridController', {
    requires: [
        'ElectoralGroups.view.usersadd.UsersAddToElectionController'
    ],
    extend: 'Ext.app.ViewController',

    alias: 'controller.electorsgrid',

    onClickAddButton: function () 
    {
        var election = this.election,
            usersadd = Ext.create('ElectoralGroups.view.usersadd.UsersAdd', {
                items: {
                    xclass: 'Aes.view.usersgrid.OperableUsersGrid',
                    controller: 'add-to-election',
                    border: false
                },
                
                viewModel: {
                    data: {
                        election: election,
                        electors: this.electors
                    }
                },
                
                storeScopes: { 
                    notElector: {
                        election_id: election.get('id')
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
                        this.electors.getById(
                            parseInt(sel.get('elector').id)
                        )
                    );
                }, this);
                
                this.electors.remove(models);
                this.electors.sync({ 
                    success: function(){
                        this.view.getStore().reload();
                    },
                    scope: this
                });
            }
        }, this);
    },
    
    onClickRegisterButton: function()
    {
        var me = this,
            grid = this.view,
            mainView = grid.up('app-main');
    
        mainView.mask();
        Ext.Ajax.request({
            url: '/index-test.php/election/registerElectorsFromGroups',
            params: {
                election_id: ElectoralGroups.app.options.electionId
            },
            success: function(response) {
                var respData = Ext.decode(response.responseText),
                    title = 'Operation finished successfully',
                    icon = Ext.Msg.OK,
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
                me.electors.reload();
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
    },
    
    init: function() 
    {
        var me = this,
            grid = this.view;
        
        this.election = ElectoralGroups.model.Election.load(
            ElectoralGroups.app.options.electionId,
            {
                success: function() {
                    this.electors = Ext.create('ElectoralGroups.store.Electors', {
                        filters: [
                            { property: 'election_id', value: this.election.get('id') }
                        ],
                        remoteFilter: true,
                        autoSync: false
                    });
                    this.electors.load();
                },
                scope: this
            }
        );        
        
        grid.getStore().setFilters([
            {
                property: 'applyScopes',
                value: Ext.encode({
                    elector: {
                        election_id: this.election.get('id')
                    }
                })
            }
        ]);
        grid.getStore().setRemoteFilter(true);
    
        this.view.getSelectionModel().on('selectionchange', function(selModel, selections){
            grid.down('#removeButton').setDisabled(selections.length === 0);
        });
        
        var groups = ElectoralGroups.app.getStore('VoterGroups'),
            hasAssignedGroups = function() {
                return !!(groups.find('assigned', true) >= 0);
            },
            registrationCheck = function() {
                me.view.down('#registerButton').setDisabled(!hasAssignedGroups());
            };
        
        groups.on('update', registrationCheck, this);
        groups.on('load', registrationCheck, this);
    }
});

