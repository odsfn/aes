/**
 * VoterGroupMember is a user registered in a VoterGroup
 */
Ext.define('Aes.model.VoterGroupMember', function(VoterGroupMember) {
    return {
        extend: 'Aes.model.Base',
        requires: [
            'Aes.model.Base',
            'Aes.UrlHelper'
        ],
        fields: [
            'voter_group_id', 'user_id', 'created_ts'
        ],
        proxy: {
            type: 'AesRest',
            url: Aes.UrlHelper.getBaseUrl() + 'api/voterGroupMember'
        }
    };
});