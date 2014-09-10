Ext.define('ElectoralGroups.view.usersadd.UsersAddToElectionController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.add-to-election',

    onAddBtnClicked: function() 
    {
        var window = this.getView().up('window'),
            grid = this.getView().down('grid'),
            sel = grid.getSelectionModel(),
            election = this.getView().up().getViewModel().data.election,
            electors = this.getView().up().getViewModel().data.electors;
            
            done = function() {
                grid.getStore().reload();
                window.unmask();
            };
   
        window.mask();

        var usersToAdd = [];
        Ext.each(sel.getSelection(), function(user, index) {
            var newMember = Ext.create('ElectoralGroups.model.Elector', {
                election_id: election.get('id'),
                user_id: user.get('user_id')
            });
            usersToAdd.push(newMember);
        });
        
        electors.add(usersToAdd);
        electors.sync({
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

