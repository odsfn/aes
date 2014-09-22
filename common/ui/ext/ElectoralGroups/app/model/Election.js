/* 
 * Election model
 */
Ext.define('ElectoralGroups.model.Election', function(Election) {
    return {
        requires: ['ElectoralGroups.model.Base'],
        
        extend: 'ElectoralGroups.model.Base',
        
        fields: [
            'name', 'user_id', 'status', 'mandate', 'voter_group_restriction',
            'voter_reg_type', 'voter_reg_confirm'
        ]
    };
});