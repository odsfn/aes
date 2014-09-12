Ext.define('Aes.UrlHelper', function(UrlHelper) {
    var baseUrl = window.appConfig.baseUrl;
    
    return {
        statics: {
            getBaseUrl: function() {
                return baseUrl;
            }
        }
    }
});

