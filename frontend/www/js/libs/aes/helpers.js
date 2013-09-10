/*
 * Common client-side helpers
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */

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
Backbone.Model.prototype.parse = function(response, options) {
    
    if(!_.isObject(response.data))  //Parsing response in context of collection's fetch 
        return response;
    
    if(!response.success) {
        if(response.message)
            alert('Error: ' + response.message);
        else
            alert('Error: Invalid response format');
    }
    
    if(response.data.totalCount === 1)
        return response.data.models[0];
    else if(response.totalCount === 0)
        return {};
};
        
Backbone.Collection.prototype.parse = function(response, options) {
    if(!response.success) {
        if(response.message)
            alert('Error: ' + response.message);
        else
            alert('Error: Invalid response format');
    }
    
    return response.data.models;
};