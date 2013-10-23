/* 
 * WebUser model. 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var WebUser = function (config) {
    
    var 
    
    config = config || {},
    
    publicProps = {
    
        id: null,
                
        displayName: '',
        
        authToken: ''
    },
    
    publicMethods = {

        isGuest: function() {
            return !this.isAuthenticated();
        },

        isAuthenticated: function() {
            if(this.id) {
                return true;
            }

            return false;
        }
    
    };
    
    $.extend(this, publicProps, config, publicMethods);
};

WebUser.getInstace = function() {
    return webUser;
};