/* 
 * WebUser model. 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var WebUser = (function(){
    
    var id, displayName, authToken, roles = [], accessRules = {};
    
    return {
        
        getId: function() {
            return id;
        },
        
        hasRole: function(role) {
            return !(_.indexOf(roles, role) == -1);
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
            
        hasAccess: function(action, obj) {
            var checker = false, curNode;
            
            var path = action.split('.'),
                rulesHash = accessRules;
            
            while(path.length > 0) {
                curNode = path.shift();
                
                if(! (rulesHash = rulesHash[curNode]) )
                    throw new Error('Checking access rule was not declared in accessRules');
            }
            
            checker = rulesHash;
            
            if(checker && checker.call(WebUser, obj))
                return true;
            
            return false;
        },        
             
        setAccessRules: function(rules) {
            accessRules = rules;
        },
                
        setRoles: function(role) {
            if(!_.isArray(role))
                roles = [role];
            else
                roles = role;
        },
                
        addRoles: function(rolesToAdd) {
            if(!_.isArray(rolesToAdd))
                rolesToAdd = [rolesToAdd];
            
            roles = _.uniq(roles.concat(rolesToAdd));
        },        
                
        addAccessRules: function(rules) {
            accessRules = _.extend({}, accessRules, rules);
        },
                
        initialize: function(config) {
            id = config.id;
            displayName = config.displayName || '';
            authToken = config.authToken || '';
            
            if(config.roles)
                this.setRoles(config.roles);
            
            if(config.accessRules)
                accessRules = config.accessRules;
        }
    };
    
})();