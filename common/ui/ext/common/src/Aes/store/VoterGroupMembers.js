Ext.define('Aes.store.VoterGroupMembers', {
    requires: [
        'Aes.model.VoterGroupMember'
    ],
    extend: 'Ext.data.Store',
    alias: 'store.VoterGroupMembers',
    autoSync: true,
    model: 'Aes.model.VoterGroupMember'
});


