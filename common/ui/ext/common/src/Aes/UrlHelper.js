Ext.define('Aes.UrlHelper', function(UrlHelper) {
    if(window.appConfig && window.appConfig.baseUrl)
        var baseUrl = window.appConfig.baseUrl;
    else
        var baseUrl = '';
    
    return {
        statics: {
            getBaseUrl: function() {
                return baseUrl;
            }
        }
    }
});

