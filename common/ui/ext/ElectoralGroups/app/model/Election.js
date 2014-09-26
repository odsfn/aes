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
        ],
        
        statics: {
            VOTER_REG_TYPE_SELF: 0,
    
            VOTER_REG_TYPE_ADMIN: 1,
    
            VOTER_REG_CONFIRM_NOTNEED: 0,
    
            VOTER_REG_CONFIRM_NEED: 1, 
            
            VGR_NO: 0,
    
            VGR_GROUPS_ONLY: 1,
    
            VGR_GROUPS_ADD: 2,
        }
    };
});