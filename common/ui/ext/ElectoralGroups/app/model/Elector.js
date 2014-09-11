/**
 * Elector
 */
Ext.define('ElectoralGroups.model.Elector', function(Elector) {
    return {
        extend: 'ElectoralGroups.model.Base',
        requires: ['ElectoralGroups.model.Base'],
        fields: [
            'election_id', 'user_id', 'status'
        ]
    };
});