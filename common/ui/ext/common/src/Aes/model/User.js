Ext.define('Aes.model.User', {
    requires: [
        'Aes.UrlHelper'
    ],
    
    extend: 'Ext.data.Model',
    
    idProperty: 'user_id',
    
    fields: [
        { name: 'user_id', type: 'int', 'default': null },
        { name: 'first_name' }, { name: 'last_name' }, { name: 'email' }, 
        { name: 'birth_place' }, { name: 'mobile_phone' }, { name: 'photo' },
        { name: 'photo_thmbnl_64' },
        {
            name: 'gender',
            convert: function(v, r) {
                var res = '-';
                if (v == 1)
                    res = 'Male';
                else if (v == 2)
                    res = 'Female';
                
                return res;
            },
            serialize: function(v, r) {
                var res = 0;
                if (v === 'Male')
                    res = 1;
                else if (v === 'Female')
                    res = 2;
                
                return res;
            }
        }, 
        { name: 'birth_day', type: 'date', dateFormat: 'timestamp' }
    ],
    
    proxy: {
        type: 'AesRest',
        url: Aes.UrlHelper.getBaseUrl() + 'api/profile'
    }
});