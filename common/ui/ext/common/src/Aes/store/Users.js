Ext.define('Aes.store.Users', {
    requires: ['Aes.model.User'],
    extend: 'Ext.data.Store',
    alias: 'store.users',
    model: 'Aes.model.User',
    pageSize: 25
});