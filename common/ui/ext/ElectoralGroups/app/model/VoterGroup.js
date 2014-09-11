Ext.define('ElectoralGroups.model.VoterGroup', function(VoterGroup) {
    return {
        extend: 'ElectoralGroups.model.Base',
        requires: [
            'ElectoralGroups.model.Base',
            'ElectoralGroups.store.ElectionVoterGroups'
        ],
        fields: [
            'name', 
            {
                name: 'type',
                type: 'string',
                'default': 'Global',
                convert: function(val, rec) {
                    return rec.getTypeLabel(val);
                },
                serialize: function(val, rec) {
                    return rec.getTypeId(val);
                }
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
            'status', 'user_id', 'created_ts'
        ],
        
        validators: [{
            type: 'length',
            field: 'name',
            min: 1
        }],

        getTypeLabel: function(id) {
            return VoterGroup.types()[id];
        },

        getTypeId: function(label) {
            return VoterGroup.types().indexOf(label);
        },
        
        statics: {
            types: function() {
                return ['Global', 'Local'];
            }
        }
    };
});