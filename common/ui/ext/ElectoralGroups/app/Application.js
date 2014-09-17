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
        'ElectoralGroups.view.main.Main'
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
    },
    
    options: {
        userId: null,
        electionId: null
    }
});
