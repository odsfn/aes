Ext.define('Aes.store.VoterGroups', {
    requires: ['Aes.model.VoterGroup'],
    extend: 'Ext.data.Store',
    alias: 'store.VoterGroups',
    autoLoad: false,
    autoSync: true,
    model: 'Aes.model.VoterGroup',
    pageSize: 25,
    proxy: {
        type: 'AesRest',
        url: Aes.UrlHelper.getBaseUrl() + 'api/voterGroup'
    }
});


