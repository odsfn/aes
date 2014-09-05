/**
 * VoterGroupMember is a user registered in a VoterGroup
 */
Ext.define('ElectoralGroups.model.VoterGroupMember', function(Voter) {
    return {
        extend: 'ElectoralGroups.model.Base',
        
        fields: [
            'voter_group_id', 'user_id', 'created_ts',
            { name: 'first_name', mapping: 'profile.first_name' }, 
            { name: 'last_name', mapping: 'profile.last_name' }, 
            { name: 'email', mapping: 'profile.email' }, 
            { name: 'birth_place', mapping: 'profile.birth_place' }, 
            { name: 'mobile_phone', mapping: 'profile.mobile_phone' }, 
            { name: 'photo', mapping: 'profile.photo' },
            { name: 'photo_thmbnl_64', mapping: 'profile.photo_thmbnl_64' },
            {
                name: 'gender',
                mapping: 'profile.gender',
                convert: function(v, r) {
                    var res = '-';
                    if (v == 1)
                        res = 'Male';
                    else if (v == 2)
                        res = 'Female';

                    return res;
                },
                serialize: function(v, r) {
                    var res = 0;
                    if (v === 'Male')
                        res = 1;
                    else if (v === 'Female')
                        res = 2;

                    return res;
                }
            }, 
            {   
                name: 'birth_day', type: 'date', mapping: 'profile.birth_day',
                dateFormat: 'timestamp' 
            }
        ]
    };
});