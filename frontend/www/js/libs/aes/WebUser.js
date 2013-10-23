/* 
 * WebUser model. 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var WebUser = (function(){
    
    var id, displayName, authToken;
    
    return {
        
        getId: function() {
            return id;
        },
        
        isGuest: function() {
            return !this.isAuthenticated();
        },

        isAuthenticated: function() {
            if(id) {
                return true;
            }

            return false;
        },
                
        initialize: function(config) {
            id = config.id;
            displayName = config.displayName;
            authToken = config.authToken;
        }
    };
    
})();