Ext.define('ElectoralGroups.model.VoterGroup', function(VoterGroup) {
    return {
        extend: 'ElectoralGroups.model.Base',
        requires: [
            'ElectoralGroups.model.Base',
            'ElectoralGroups.store.ElectionVoterGroups'
        ],
        fields: [
            {
                name: 'name',
                type: 'string'
            },
            {
                name: 'type',
                type: 'int'
            },
            {
                name: 'typeLabel',
//                type: 'string',
//                'default': 'Local',
                calculate: function(data) {
                    return VoterGroup.getTypeLabel(data.type);
//                    console.log('Calculating "type" property which is "' + data.type 
//                        + '" for group "' + data.name + '"' );
//                    return VoterGroup.getTypeLabel(data.type);
                },
//                serialize: function(val, rec) {
//                    console.log('Serializing "type" property which is "' + val 
//                        + '" for group "' + rec.get('name') + '"' );
//                    return VoterGroup.getTypeId(val);
//                },
                persist: false
            },
            {
                name: 'assigned',
                type: 'boolean',
                'default': false,
                persist: false,
                calculate: function(data) {
                    var assigned = ElectoralGroups.app.getStore('ElectionVoterGroups').findRecord('voter_group_id', data.id);
                    
                    return !!assigned;
                }
            },
            {
                name: 'user_id',
                type: 'int'
            },
            {
                name: 'election_id',
                type: 'int'
            },
            {
                name: 'status',
                type: 'int'
            }, 
            {
                name: 'created_ts',
                type: 'date'
            }
        ],
        
        validators: [{
            type: 'length',
            field: 'name',
            min: 1
        }],
        
        statics: {
            types: function() {
                return ['Global', 'Local'];
            },
            
            getTypeLabel: function(id) {
                return VoterGroup.types()[id];
            },

            getTypeId: function(label) {
                return VoterGroup.types().indexOf(label);
            }            
        }
    };
});