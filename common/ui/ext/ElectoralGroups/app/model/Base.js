Ext.define('ElectoralGroups.model.Base', {
    requires: [
        'Aes.model.Base',
        'Aes.data.proxy.Rest',
        'Aes.UrlHelper'
    ],
    
    extend: 'Aes.model.Base',

    schema: {
        namespace: 'ElectoralGroups.model',

        proxy: {
            type: 'AesRest',
            url: Aes.UrlHelper.getBaseUrl() + 'api/{entityName}'
        }
    }
});