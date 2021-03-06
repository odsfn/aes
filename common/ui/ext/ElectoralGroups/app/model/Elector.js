/**
 * Elector
 */
Ext.define('ElectoralGroups.model.Elector', function(Elector) {
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
            'status'
        ],
        statics: {
            STATUS_ACTIVE: 0,
            STATUS_NEED_APPROVE: 1,
            STATUS_BLOCKED: 2
        }
    };
});