Ext.define('ElectoralGroups.store.VoterGroups', {
    requires: ['ElectoralGroups.model.VoterGroup'],
    extend: 'Ext.data.Store',
    alias: 'store.VoterGroups',
    autoLoad: false,
    autoSync: true,
    model: 'ElectoralGroups.model.VoterGroup',
    pageSize: 25,
    proxy: {
        type: 'AesRest',
        url: Aes.UrlHelper.getBaseUrl() + 'api/voterGroup'
    }
});


