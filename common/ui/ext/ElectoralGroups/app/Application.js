/**
 * The main application class. An instance of this class is created by app.js when it calls
 * Ext.application(). This is the ideal place to handle application launch and initialization
 * details.
 */
Ext.define('ElectoralGroups.Application', {
    extend: 'Ext.app.Application',
    
    requires: [
        'ElectoralGroups.model.Election',
        'Aes.store.Users',
        'ElectoralGroups.store.Electors',
        
        'Ext.data.validator.*',
        'ElectoralGroups.view.main.Main',
        
        'ElectoralGroups.view.RequestsGrid'
    ],
    
    name: 'ElectoralGroups',

    stores: [
        'VoterGroups', 'ElectionVoterGroups'
    ],
    
    controllers: [
        'Root'
    ],
    
    init: function(app) {
        window.appConfig = window.appConfig || {}; 
        app.options = Ext.clone(window.appConfig);
        app.election = new ElectoralGroups.model.Election();
        app.election.set(app.options.election);
    },
    
    launch: function () {
        var voterGroups = this.getStore('VoterGroups');
        voterGroups.setFilters([{
            property: 'electionScope',
            value: {
                election_id: ElectoralGroups.app.options.electionId
            }
        }]);
        voterGroups.setRemoteFilter(true);
        voterGroups.setSorters([
            {
                property: 'assigned',
                direction: 'DESC'
            }
        ]);
        
        var voterGroupAssignments = this.getStore('ElectionVoterGroups');
        voterGroupAssignments.setFilters([{
            property: 'election_id',
            value: this.options.electionId
        }]);
        voterGroupAssignments.setRemoteFilter(true);
        
        if(this.election.get('voter_reg_type') != ElectoralGroups.model.Election.VOTER_REG_TYPE_ADMIN
            && this.election.get('voter_reg_confirm') == ElectoralGroups.model.Election.VOTER_REG_CONFIRM_NEED) 
        {
            var tabPanel = Ext.ComponentQuery.query('app-main > tabpanel')[0];
            tabPanel.add({
                itemId: 'registration-requests',
                title: 'Registration Requests',
                xtype: 'requestsgrid',
                closable: false
            });
        }
    },
    
    options: {
        userId: null,
        electionId: null
    },
    
    election: null
});
