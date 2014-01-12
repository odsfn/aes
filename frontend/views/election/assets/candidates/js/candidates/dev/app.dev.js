/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
$(function(){
       fauxServer.setLatency(300, 2000);
    
    var 
        timestamp = function(time) {
            var time = time || new Date();
            return Math.round(time.getTime() / 1000);
        },
        
        wrapResponse = function(result, success, message) {
            var 
                message = message || 'Ok',
                success = success || true
                result = result || {};
            
            if(result.hasOwnProperty('models') && result.hasOwnProperty('totalCount')) {
                respData = result;
            }else{
                respData = {
                    totalCount: 1,
                    models: [result]
                };
            }
            
            return {
                success: success,
                message: message,
                data: respData
            };
        },
      
      n = 1,
      c = 1,
      
      fixCands = new Backbone.Collection([]);
      
      fauxServer.addRoute('createCand', UrlManager.createUrl('api/electionCandidate'), 'POST', function(context) {
        var time = new Date(),
           ts = time.getTime();

        context.data.id = _.uniqueId();
        
        var res = c % 2;
        
        if(res === 0) {
            context.data.number = n++;
            context.data.status = 'registered';
        }else{
            context.data.number = null;
            context.data.status = 'invited';
        }
        
        c++;
//        context.data = _.extend(context.data, { 
//           created_ts: ts
//        });
        fixCands.add(context.data);

        return wrapResponse(context.data);
      });
});

