/**
 * VoterGroupMember is a user registered in a VoterGroup
 */
Ext.define('ElectoralGroups.model.VoterGroupMember', function(VoterGroupMember) {
    return {
        extend: 'ElectoralGroups.model.Base',
        
        fields: [
            'voter_group_id', 'user_id', 'created_ts'
        ]
    };
});