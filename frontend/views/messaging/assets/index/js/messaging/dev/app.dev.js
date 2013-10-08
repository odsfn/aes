/* 
 * Isolated version from server
 */
$(function(){
    console.log('In app.dev.js');
    console.log("fauxServer version: " + fauxServer.getVersion());
    
//    fauxServer.enable(false);
    
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
   
        fixtureConvs = new Backbone.Collection([
            {
                id: curId = _.uniqueId(),
//                title: 'Conversation 1',
                created_ts: time = (new Date('Sep 25 2013 11:19:33')).getTime(),
                messages: [
                    {
                        id: _.uniqueId(),
                        conversation_id: curId,
                        created_ts: time,
                        text: 'Cnvs_text_1. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.',
                        user_id: 2
                    }
                ],
                participants: [
                    {
                        conversation_id: curId,
                        user_id: 1,
                        user: {
                            user_id: 1,
                            photo: '',
                            displayName: 'Vasiliy Pedak'
                        },
                        last_view_ts: null
                    },
                    {
                        conversation_id: curId,
                        user_id: 2,
                        user: {
                            user_id: 2,
                            photo: '',
                            displayName: 'Another User'
                        },
                        last_view_ts: null                       
                    }
                ],
                initiator_id: 2
            },
            
            {
                id: curId = _.uniqueId(),
//                title: '',
                created_ts: time = (new Date('Sep 24 2013 13:49:03')).getTime(),
                messages: [
                    {
                        id: _.uniqueId(),
                        conversation_id: curId,
                        created_ts: time,
                        text: 'Cnvs_text_2. The collection\'s comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.',
                        user_id: 3
                    }
                ],
                participants: [
                    {
                        conversation_id: curId,
                        user_id: 1,
                        user: {
                            user_id: 1,
                            photo: '',
                            displayName: 'Vasiliy Pedak'
                        },
                        last_view_ts: null
                    },
                    {
                        conversation_id: curId,
                        user_id: 3,
                        user: {
                            user_id: 3,
                            photo: '',
                            displayName: 'John Lennon'
                        },
                        last_view_ts: null                       
                    }
                ],
                initiator_id: 3
            },

            {
                id: curId = _.uniqueId(),
//                title: 'Conversation 3',
                created_ts: time = (new Date('23 Sep 2013 1:10:01')).getTime(),
                messages: [
                    {
                        id: _.uniqueId(),
                        conversation_id: curId,
                        created_ts: time,
                        text: 'Cnvs_text_3. The collection\'s comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.',
                        user_id: 4
                    }
                ],
                participants: [
                    {
                        conversation_id: curId,
                        user_id: 1,
                        user: {
                            user_id: 1,
                            photo: '',
                            displayName: 'Vasiliy Pedak'
                        },
                        last_view_ts: time + 60
                    },
                    {
                        conversation_id: curId,
                        user_id: 4,
                        user: {
                            user_id: 4,
                            photo: '',
                            displayName: 'Steve Jobs'
                        },
                        last_view_ts: null                       
                    }
                ],
                initiator_id: 4
            },

            {
                id: curId = _.uniqueId(),
                title: '',
                created_ts: time = (new Date('21 Sep 2013 18:11:33')).getTime(),
                messages: [
                    {
                        id: _.uniqueId(),
                        conversation_id: curId,
                        created_ts: time,
                        text: 'Cnvs_text_4. The collection\'s comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.',
                        user_id: 1
                    }
                ],
                participants: [
                    {
                        conversation_id: curId,
                        user_id: 1,
                        user: {
                            user_id: 1,
                            photo: '',
                            displayName: 'Vasiliy Pedak'
                        },
                        last_view_ts: time + 60
                    },
                    {
                        conversation_id: curId,
                        user_id: 5,
                        user: {
                            user_id: 5,
                            photo: '',
                            displayName: 'Andrew Stuart Tanenbaum'
                        },
                        last_view_ts: time                       
                    }
                ],
                initiator_id: 1
            },

            {
                id: curId = _.uniqueId(),
//                title: 'Conversation 5',
                created_ts: time = (new Date('21 Sep 2013 11:19:33')).getTime(),
                messages: [
                    {
                        id: _.uniqueId(),
                        conversation_id: curId,
                        created_ts: time,
                        text: 'Cnvs_text_5. The collection\'s comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.',
                        user_id: 2
                    }
                ],
                participants: [
                    {
                        conversation_id: curId,
                        user_id: 1,
                        user: {
                            user_id: 1,
                            photo: '',
                            displayName: 'Vasiliy Pedak'
                        },
                        last_view_ts: time + 60
                    },
                    {
                        conversation_id: curId,
                        user_id: 2,
                        user: {
                            user_id: 2,
                            photo: '',
                            displayName: 'Another User'
                        },
                        last_view_ts: null                       
                    }
                ],
                initiator_id: 2
            },

            {
                id: curId = _.uniqueId(),
//                title: 'Conversation 6',
                created_ts: time = (new Date('21 Sep 2013 11:00:33')).getTime(),
                messages: [
                    {
                        id: _.uniqueId(),
                        conversation_id: curId,
                        created_ts: time,
                        text: 'Cnvs_text_6. The collection\'s comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.',
                        user_id: 1
                    }
                ],
                participants: [
                    {
                        conversation_id: curId,
                        user_id: 1,
                        user: {
                            user_id: 1,
                            photo: '',
                            displayName: 'Vasiliy Pedak'
                        },
                        last_view_ts: time + 60
                    },
                    {
                        conversation_id: curId,
                        user_id: 2,
                        user: {
                            user_id: 2,
                            photo: '',
                            displayName: 'Another User'
                        },
                        last_view_ts: null                       
                    }
                ],
                initiator_id: 2
            }
        ]),
                
        fixtureMsgs = new Backbone.Collection([
            fixtureConvs.at(0).get('messages')[0],
            fixtureConvs.at(1).get('messages')[0],
            fixtureConvs.at(2).get('messages')[0],
            {
                id: _.uniqueId(),
                conversation_id: fixtureConvs.at(2).id,
                created_ts: (new Date('25 Jun 2013 13:30:33')).getTime(),
                text: 'C3M2. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna.',
                user_id: 1
            },
            {
                id: _.uniqueId(),
                conversation_id: fixtureConvs.at(2).id,
                created_ts: (new Date('25 Jun 2013 10:32:03')).getTime(),
                text: 'C3M3. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna.',
                user_id: 4
            },
            {
                id: _.uniqueId(),
                conversation_id: fixtureConvs.at(2).id,
                created_ts: (new Date('25 Jun 2013 7:13:00')).getTime(),
                text: 'C3M4. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna.',
                user_id: 1
            },
            {
                id: _.uniqueId(),
                conversation_id: fixtureConvs.at(2).id,
                created_ts: (new Date('24 Jun 2013 18:56:33')).getTime(),
                text: 'C3M5. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna.',
                user_id: 1
            },
            {
                id: _.uniqueId(),
                conversation_id: fixtureConvs.at(2).id,
                created_ts: (new Date('24 Jun 2013 16:01:33')).getTime(),
                text: 'C3M6. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna.',
                user_id: 4
            },            
            fixtureConvs.at(3).get('messages')[0],
            fixtureConvs.at(4).get('messages')[0],
            fixtureConvs.at(5).get('messages')[0]
        ], {
            comparator: function(model) {
                return -model.get('created_ts');
            }
        });
    
    fauxServer.get(UrlManager.createUrl('api/conversation'), function(context) {
        var responseObj,
            convsToReturn,
            filteredConvs = fixtureConvs;
        
//        if(filterUserId) {
//            filteredConvs = fixtureConvs.where({user_id :filterUserId});
//        }
        
        var filters = context.data.filter;
        
        if(filters.initiator_id == 1 && filters.participant_id == 2) {
            
            responseObj = {
                models: (new Backbone.Collection([fixtureConvs.at(0)])).toJSON(),
                totalCount: 1
            };
            
        } else if(filters.initiator_id == 1 && filters.participant_id == 5) {
            
            responseObj = {
                models: (new Backbone.Collection([fixtureConvs.at(3)])).toJSON(),
                totalCount: 1
            };
            
        } else {
        
            convsToReturn = new Backbone.Collection(filteredConvs.slice(context.data.offset, context.data.offset + context.data.limit));

            responseObj = {
                models: convsToReturn.toJSON(),
                totalCount: filteredConvs.length
            };
        }
        
        return wrapResponse(responseObj);
    });
    
    fauxServer.get(UrlManager.createUrl('api/message'), function(context) {
        var responseObj,
            msgsToReturn,
            convId,
            filteredMsgs = fixtureMsgs;
        
        if(convId = context.data.filter.conversation_id) {
            filteredMsgs = fixtureMsgs.where({conversation_id: convId});
        }
        
        msgsToReturn = new Backbone.Collection(filteredMsgs.slice(context.data.offset, context.data.offset + context.data.limit));
        
        responseObj = {
            models: msgsToReturn.toJSON(),
            totalCount: filteredMsgs.length
        };
        
        return wrapResponse(responseObj);
    });
    
    fauxServer.addRoute('createMessage', UrlManager.createUrl('api/message'), 'POST', function(context) {
       var time = new Date(),
           ts = time.getTime();
       
       context.data.id = _.uniqueId();
       context.data = _.extend(context.data, { 
           created_ts: ts
       });
       fixtureMsgs.add(context.data);
       
       return wrapResponse(context.data);
    });

    fauxServer.addRoute('createConv', UrlManager.createUrl('api/conversation'), 'POST', function(context) {
       var time = new Date(),
           ts = time.getTime();
       
       context.data.id = _.uniqueId();
       context.data = _.extend(context.data, { 
           created_ts: ts,
           participants: [
               {
                   conversation_id: context.data.id,
                   user_id: context.data.participants[0].user_id,
                   user: {
                       user_id: context.data.participants[0].user_id,
                       photo: '',
                       displayName: 'Vasiliy Pedak'
                   }
               },
               {
                   conversation_id: context.data.id,
                   user_id: context.data.participants[1].user_id,
                   user: {
                       user_id: context.data.participants[1].user_id,
                       photo: '',
                       displayName: 'Username' + context.data.participants[1].user_id + ' Lastname'
                   }
               }
           ]
       });
       fixtureMsgs.add(context.data);
       
       return wrapResponse(context.data);
    });    
    
    App.module('Messaging').setOptions({
        convsLimit: 4,
    });
    
    App.module('Messaging.Chat').setOptions({
        messagesLimit: 4
    });
    
    App.start();
});
