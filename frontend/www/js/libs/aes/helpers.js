/*
 * Common client-side helpers
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Aes = (function() {
    
    var ajaxFormatCheck = true;
    
    var defaultHandleErrorOutput = 'console';
    
    var handleAjaxError = function(message, response, xhr) {
        if(defaultHandleErrorOutput !== 'console')
            alert(message);
        else
            console.log(message);
    };
    
    function onAjaxComplete(e, xhr, options) {
        
        if(options.url.match(/^.*\.php\/api\/.*$/)) {
            
            if(xhr.status === 200 || xhr.status === 201) {
                //proccess error message
                try{
                    var response = $.parseJSON(xhr.responseText);
                } catch(e) {
                    var response = { success: false, result: xhr.responseText };
                    
                    if(response.result.match(/id="LoginForm"/m)) {
                        window.location.href = UrlManager.createUrl('userAccount/login');
                        return;
                    }
                }
                
                if(!response.success) {
                    if(response.message)
                        handleAjaxError('Error:' + response.message, response, xhr);
                    else
                        handleAjaxError('Error: Invalid response format', response, xhr);
                }
                
            } else if(xhr.status === 302) { //redirect
                
                var url = xhr.getResponseHeader('Location'); 
                
                window.location.replace(url);
                
            } else {
                handleAjaxError('XHR Error: Unexpected response status ('+ xhr.status +')', {}, xhr);
            }
            
        }
    }
    
    function bindErrorHandler() {
        $(document).bind('ajaxComplete', onAjaxComplete);
    }
    
    
    if(ajaxFormatCheck)
        bindErrorHandler();
    
    return {
                
        enableAjaxErrorsHandling: function() {
            if(ajaxFormatCheck)
                return;
            
            bindErrorHandler();
            ajaxFormatCheck = true;
        },
                
        disableAjaxErrorsHandling: function() {
            if(!ajaxFormatCheck)
                return;
            
            $(document).unbind('ajaxComplete', onAjaxComplete);
            ajaxFormatCheck = false;
        },
                
        setAjaxErrorHandler: function(handler) {
            handleAjaxError = handler;
        },
                
        escapeTags: function(inputStr) {
            return $("<div></div>").text(inputStr).html();
        }
        
    };
})();

var 
    UrlManager = function() {
        var 
            baseUrl = '',
                    
            url = function(route) {
                return baseUrl + '/' + route; 
            };
            
        return {
            setBaseUrl: function(url) {
                baseUrl = url;
            },
            createUrlCallback: function(route) {
                return function() { 
                    return url(route); 
                };
            },
            createUrl: function(route) {
                return url(route); 
            }
        };
    }();

// Overriding parsing to correctly connect with restfullyii response format
Backbone.Model.prototype.parse = function(rawData, options) {
    
    if(_.isObject(rawData) && ( !_.has(rawData, 'success') || !_.has(rawData, 'message') || !_.has(rawData, 'data') )) {        //
        return rawData;
    }
    
    var response = rawData;
    
    if(!_.isObject(response.data))  //Parsing response in context of collection's fetch 
        return response;
    
    var result = {};
    
    if(parseInt(response.data.totalCount) === 1) {
        
        if(_.isArray(response.data.models))
            result = response.data.models[0];
        else
            result = response.data.models;
        
    }
    
    return result;
};
        
Backbone.Collection.prototype.parse = function(rawData, options) {
    
    if(_.isObject(rawData) && ( !_.has(rawData, 'success') || !_.has(rawData, 'message') || !_.has(rawData, 'data') )) {        //
        return rawData;
    }
    
    return rawData.data.models;
};