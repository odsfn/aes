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
                
        FxPosts = Backbone.Collection.extend({
            comparator: function(model) {
                return -model.get('createdTs');
            }
        }),
        
        fixturePosts = new FxPosts([
        {
            id: curId = _.uniqueId(),
            reply_to: null,
            user_id: 1,
            user: {
                id: 1,
                displayName: 'Vasiliy Pedak',
                photo: 'http://placehold.it/64x64',
            },
            content: "When creating a Collection, you may choose to pass in the initial array of models. The collection's comparator may be included as an option. Passing false as the comparator option will prevent sorting. If you define an initialize function, it will be invoked when the collection is created.",
            
            displayTime: "Aug 8, 2013 10:42:00 AM",
            createdTs: 1376577786,
            
            rates: [
                {
                    id: _.uniqueId(),
                    user_id: 1,
                    post_id: curId,
                    score: 1,
                    createdTs: 1376577886
                },
                {
                    id: _.uniqueId(),
                    user_id: 2,
                    post_id: curId,
                    score: 1,
                    createdTs: 1376577886
                },
            ],
            
            comments: []
        },
        
        {
            id: curId = _.uniqueId(),
            reply_to: null,
            user_id: 1,
            user: {
                id: 1,
                displayName: 'Vasiliy Pedak',
                photo: 'http://placehold.it/64x64',
            },
            content: "Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            
            displayTime: "Aug 8, 2013 7:13:00 PM",
            createdTs: 1376577787,

            rates: [
                {
                    id: _.uniqueId(),
                    user_id: 1,
                    post_id: curId,
                    score: 1,
                    createdTs: 1376577887
                },
                {
                    id: _.uniqueId(),
                    user_id: 3,
                    post_id: curId,
                    score: -1,
                    createdTs: 1376577888
                },
                {
                    id: _.uniqueId(),
                    user_id: 4,
                    post_id: curId,
                    score: -1,
                    createdTs: 1376577889
                },
            ],
            
            comments: [
                {
                    id: _.uniqueId(),
                    reply_to: curId,
                    user_id: 2,
                    user: {
                        displayName: 'Another User',
                        photo: "http://placehold.it/64x64"
                    },
                    content: "Lorem ipsum dolor sit amet, at debet dolores est.",
                    displayTime: "Aug 8, 2013 7:46:00 PM",
                    createdTs: 1376577788,                
                },
                
                {
                    id: _.uniqueId(),
                    reply_to: curId,
                    user_id: 1,
                    user: {
                        displayName: 'Vasiliy Pedak',
                        photo: "http://placehold.it/64x64"
                    },
                    content: "At debet dolores est. Lorem ipsum dolor sit amet",
                    displayTime: " Aug 8, 2013 10:11:00 PM",
                    createdTs: 1376577789,                   
                }
            ]
        },
        
        {
            id: _.uniqueId(),
            reply_to: null,
            user_id: 3,
            user: {
                displayName: 'Jhon Lenon',
                photo: 'http://placehold.it/64x64'
            },
            content: "The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.",
            displayTime: "Aug 14, 2013 11:42:00 AM",
            createdTs: 1376577790,
            
            comments: []
        },
        
        {
            id: _.uniqueId(),
            reply_to: null,
            user_id: 4,
            user: {
                displayName: 'Yetanother User',
                photo: 'http://placehold.it/64x64'
            },
            content: "Post 4. Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            displayTime: "Aug 7, 2013 10:12:00 AM",
            createdTs: 1376577750,
            
            comments: []           
        },
        
        {
            id: _.uniqueId(),
            reply_to: null,
            user_id: 4,
            user: {
                displayName: 'Yetanother User',
                photo: 'http://placehold.it/64x64'
            },
            content: "Post 5. Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            displayTime: "Aug 7, 2013 10:08:00 AM",
            createdTs: 1376577740,
            
            comments: []           
        },
        
        {
            id: _.uniqueId(),
            reply_to: null,
            user_id: 4,
            user: {
                displayName: 'Yetanother User',
                photo: 'http://placehold.it/64x64',
            },
            content: "Post 6. Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            displayTime: "Aug 7, 2013 10:05:00 AM",
            createdTs: 1376577730,
            
            comments: []           
        },
        
        {
            id: _.uniqueId(),
            reply_to: null,
            user_id: 4,
            user: {
                displayName: 'Yetanother User',
                photo: 'http://placehold.it/64x64'
            },
            content: "Post 7. Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            displayTime: "Aug 7, 2013 10:00:00 AM",
            createdTs: 1376577720,
            
            comments: []           
        }
    ]);
    
    fauxServer.get('/index-test.php/api/post', function(context) {
        var responseObj,
            postsToReturn,
            filterUserId = context.data.filter.usersRecordsOnly || false,
            filteredPosts = fixturePosts;
        
        if(filterUserId) {
            filteredPosts = fixturePosts.where({user_id :filterUserId});
        }
        
        postsToReturn = new Backbone.Collection(filteredPosts.slice(context.data.offset, context.data.offset + context.data.limit));
        
        responseObj = {
            models: postsToReturn.toJSON(),
            totalCount: filteredPosts.length
        };
        
        return wrapResponse(responseObj);
    });
    
    fauxServer.addRoute('createPost', '/index-test.php/api/post', 'POST', function(context) {
       var time = new Date(),
           ts = Math.round(time.getTime() / 1000);
       
       context.data.id = _.uniqueId();
       context.data = _.extend(context.data, { 
           user_id: webUser.id,
           user: {
                displayName: webUser.displayName,
                photo: 'http://placehold.it/64x64'
           },
           displayTime: $.format.date(time, 'MMM d, yyyy hh:mm:ss a'),
           createdTs: ts
       });
       fixturePosts.push(context.data);
       return wrapResponse(context.data);
    });
    
    fauxServer.addRoute('deletePost', '/index-test.php/api/post/:id', 'DELETE', function(context) {
       fixturePosts.remove(context.data);
       return wrapResponse(context.data);
    });
    
    fauxServer.addRoute('updatePost', '/index-test.php/api/post/:id', 'PUT', function(context) {
       var time = new Date(),
           ts = Math.round(time.getTime() / 1000);
           
       context.data.editedTs = ts;
       fixturePosts.set(context.data);
       return wrapResponse(context.data);
    });
    
    fauxServer.addRoute('createPostRate', '/index-test.php/api/postRate', 'POST', function(context) {
        context.data.createdTs = timestamp();
        context.data.id = _.uniqueId();
        
        return wrapResponse(context.data);
    });
    
    fauxServer.addRoute('deletePostRate', '/index-test.php/api/postRate/:id', 'DELETE', function(context){
        return wrapResponse(context.data);
    });
    
    PostsApp.Feed.posts.limit = 3;
    
    PostsApp.start();
});
