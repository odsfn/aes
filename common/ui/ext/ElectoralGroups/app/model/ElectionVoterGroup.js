Ext.define('ElectoralGroups.model.ElectionVoterGroup', {
    extend: 'ElectoralGroups.model.Base',
    fields: [
        {
            name: 'election_id',
            type: 'int'
        },
        {
            name: 'voter_group_id',
            type: 'int',
            reference: {
                parent: 'VoterGroup',
                role: 'voterGroup'
            }
        }
    ]
});