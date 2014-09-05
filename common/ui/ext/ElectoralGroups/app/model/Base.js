Ext.define('ElectoralGroups.model.Base', {
    extend: 'Aes.model.Base',

    schema: {
        namespace: 'ElectoralGroups.model',

        proxy: {
            type: 'AesRest',
            url: '/index-test.php/api/{entityName}'
        }
    }
});