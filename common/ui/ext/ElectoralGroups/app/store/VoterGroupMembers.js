Ext.define('ElectoralGroups.store.VoterGroupMembers', {
    requires: [
        'ElectoralGroups.model.VoterGroupMember'
    ],
    extend: 'Ext.data.Store',
    alias: 'store.VoterGroupMembers',
    autoSync: true,
    model: 'ElectoralGroups.model.VoterGroupMember'
});


