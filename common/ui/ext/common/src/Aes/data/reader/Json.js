Ext.define('Aes.data.reader.Json', {
    extend: 'Ext.data.reader.Json',
    alias: 'reader.AesJson',
    rootProperty: 'data.models',
    totalProperty: 'data.totalCount',
    messageProperty: 'message'
});


