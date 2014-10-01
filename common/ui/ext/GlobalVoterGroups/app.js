/*
 * This file is generated and updated by Sencha Cmd. You can edit this file as
 * needed for your application, but these edits will have to be merged by
 * Sencha Cmd when upgrading.
 */
Ext.grid.filters.filter.Date.prototype.dateWriteFormat = 'Y-m-d';

Ext.application({
    requires: [
        "Aes.overrides.picker.Date",
        "Aes.overrides.grid.filters.filter.Date"
    ],
    
    name: 'GlobalVoterGroups',

    extend: 'GlobalVoterGroups.Application',
    
    autoCreateViewport: 'GlobalVoterGroups.view.main.Main'
	
    //-------------------------------------------------------------------------
    // Most customizations should be made to GlobalVoterGroups.Application. If you need to
    // customize this file, doing so below this section reduces the likelihood
    // of merge conflicts when upgrading to new versions of Sencha Cmd.
    //-------------------------------------------------------------------------
});
