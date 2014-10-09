Ext.define('Aes.model.VoterGroup', function(VoterGroup) {
    return {
        extend: 'Aes.model.Base',
        requires: [
            'Aes.model.Base',
            'Aes.UrlHelper'
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
                name: 'user_id',
                type: 'int'
            },
            {
                name: 'election_id',
                type: 'int',
                allowNull: true
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
        
        proxy: {
            type: 'AesRest',
            url: Aes.UrlHelper.getBaseUrl() + 'api/voterGroup'
        },
        
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