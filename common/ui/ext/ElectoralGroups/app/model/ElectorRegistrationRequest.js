/**
 * ElectorRegistrationRequest
 */
Ext.define('ElectoralGroups.model.ElectorRegistrationRequest', function(ElectorRegistrationRequest) {
    return {
        extend: 'ElectoralGroups.model.Base',
        requires: ['ElectoralGroups.model.Base'],
        fields: [
            {
                name: 'election_id',
                reference: 'Election'
            },
            { 
                name: 'user_id',
                reference: 'Aes.model.User'
            }, 
            {
                name: 'status',
                type: 'int'
            },
            {
                name: 'data',
                critical: true
            }
        ],
        statics: {
            STATUS_AWAITING_ADMIN_DECISION: 0,
            STATUS_AWAITING_USERS_DECISION: 1,
            STATUS_REGISTERED: 9,
            STATUS_DECLINED: 10
        },
        getGroups: function() {
            var groups = this.get('data').groups || [];
            Ext.each(groups, function(groupId, index) {
                groups[index] = parseInt(groupId);
            });
            
            return groups;
        }
    };
});