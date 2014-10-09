Ext.define('ElectoralGroups.store.ElectionVoterGroups', {
    requires: ['ElectoralGroups.model.ElectionVoterGroup'],
    extend: 'Ext.data.Store',
    alias: 'store.ElectionVoterGroups',
    autoSync: true,
    model: 'ElectoralGroups.model.ElectionVoterGroup',
    pageSize: 25,
    listeners: {
        add: function(store, records) {
            Ext.each(records, function(rec) {
                Ext.getStore('AssignableVoterGroups')
                        .findRecord('id', rec.get('voter_group_id'))
                        .set('assigned', true);
            });
        },
        remove: function(store, records) {
            Ext.each(records, function(rec) {
                Ext.getStore('AssignableVoterGroups')
                        .findRecord('id', rec.get('voter_group_id'))
                        .set('assigned', false);
            });
        },
        load: function(store, records) {
            Ext.each(records, function(rec) {
                var r = Ext.getStore('AssignableVoterGroups')
                        .findRecord('id', rec.get('voter_group_id'));
                
                if(r) r.set('assigned', true);
            });            
        }
    }
});


