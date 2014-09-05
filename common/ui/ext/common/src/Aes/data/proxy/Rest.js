Ext.define('Aes.data.proxy.Rest', {
    extend: 'Ext.data.proxy.Rest',
    alias: 'proxy.AesRest',
    directionParam: 'sort',
    pageParam: '',
    startParam: 'offset',
    extraParams: {
        extjs: true
    },
    reader: {
        type: 'AesJson'
    }
});
