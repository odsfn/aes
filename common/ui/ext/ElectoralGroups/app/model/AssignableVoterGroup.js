Ext.define('ElectoralGroups.model.AssignableVoterGroup', function(AssignableVoterGroup){
    return {
        requires: [
            'Aes.model.VoterGroup',
            'ElectoralGroups.store.ElectionVoterGroups'
        ],
        extend: 'Aes.model.VoterGroup',
        fields: [
            {
                name: 'assigned',
                type: 'boolean',
                'default': false,
                persist: false,
                calculate: function(data) {
                    var assigned = ElectoralGroups.app.getStore('ElectionVoterGroups').findRecord('voter_group_id', data.id);

                    return !!assigned;
                }
            }        
        ],
        
        statics: {
            TYPE_LOCAL: 1,
            TYPE_GLOBAL: 0,

            types: function() {
                return ['Global', 'Local'];
            },

            getTypeLabel: function(id) {
                return AssignableVoterGroup.types()[id];
            },

            getTypeId: function(label) {
                return AssignableVoterGroup.types().indexOf(label);
            }
        }
    };
});