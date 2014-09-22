Ext.define('ElectoralGroups.store.Electors', {
    requires: [
        'ElectoralGroups.model.Elector'
    ],
    extend: 'Ext.data.Store',
    alias: 'store.Electors',
    autoSync: true,
    model: 'ElectoralGroups.model.Elector'
});