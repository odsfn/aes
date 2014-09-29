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
                calculate: function(data) {
                    return VoterGroup.getTypeLabel(data.type);
                },
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
            TYPE_LOCAL: 1,
            TYPE_GLOBAL: 0,
            
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