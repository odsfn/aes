Ext.define('ElectoralGroups.model.Base', {
    extend: 'Aes.model.Base',

    requires: [
        'Aes.model.Base',
        'Aes.data.proxy.Rest'
    ],

    schema: {
        namespace: 'ElectoralGroups.model',

        proxy: {
            type: 'AesRest',
            url: '/index-test.php/api/{entityName}'
        }
    }
});