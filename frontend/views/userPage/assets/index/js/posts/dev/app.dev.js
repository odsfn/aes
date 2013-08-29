/* 
 * Isolated version from server
 */
$(function(){
    console.log('In app.dev.js');
    console.log("fauxServer version: " + fauxServer.getVersion());
    
//    fauxServer.enable(false);
    
    fauxServer.setLatency(300, 2000);
    
    var 
        FxPosts = Backbone.Collection.extend({
            comparator: function(model) {
                return -model.get('createdTs');
            }
        }),
        
        fixturePosts = new FxPosts([
        {
            id: _.uniqueId(),
            reply: null,
            authorId: 1,
            authorDisplayName: 'Vasiliy Pedak',
            authorPhoto: 'http://placehold.it/64x64',
            
            content: "When creating a Collection, you may choose to pass in the initial array of models. The collection's comparator may be included as an option. Passing false as the comparator option will prevent sorting. If you define an initialize function, it will be invoked when the collection is created.",
            
            displayTime: "10:42 AM 8 August, 2013",
            createdTs: 1376577786,
            
            likes: 165,
            
            dislikes: 32,
            
            comments: []
        },
        
        {
            id: curId = _.uniqueId(),
            reply: null,
            authorId: 1,
            authorDisplayName: 'Vasiliy Pedak',
            authorPhoto: 'http://placehold.it/64x64',
            
            content: "Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            
            displayTime: "7:13 PM 8 August, 2013",
            createdTs: 1376577787,
            likes: 165,
            
            dislikes: 32,
            
            comments: [
                {
                    id: _.uniqueId(),
                    reply: curId,
                    authorId: 2,
                    authorDisplayName: 'Another User',
                    authorPhoto: "http://placehold.it/64x64",
                    content: "Lorem ipsum dolor sit amet, at debet dolores est.",
                    displayTime: "7:46PM 8 August, 2013",
                    createdTs: 1376577788,
                    likes: 5,
                    dislikes: 2,                    
                },
                
                {
                    id: _.uniqueId(),
                    reply: curId,
                    authorId: 1,
                    authorDisplayName: 'Vasiliy Pedak',
                    authorPhoto: "http://placehold.it/64x64",
                    content: "At debet dolores est. Lorem ipsum dolor sit amet",
                    displayTime: "10:11PM 8 August, 2013",
                    createdTs: 1376577789,
                    likes: 0,
                    dislikes: 0,                      
                }
            ]
        },
        
        {
            id: _.uniqueId(),
            reply: null,
            authorId: 3,
            authorDisplayName: 'Jhon Lenon',
            authorPhoto: 'http://placehold.it/64x64',
            content: "The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.",
            displayTime: "11:42 AM 14 August, 2013",
            createdTs: 1376577790,
            likes: 0,
            
            dislikes: 0,
            
            comments: []
        },
        
        {
            id: _.uniqueId(),
            reply: null,
            authorId: 4,
            authorDisplayName: 'Yetanother User',
            authorPhoto: 'http://placehold.it/64x64',
            content: "Post 4. Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            displayTime: "10:12 AM 7 August, 2013",
            createdTs: 1376577750,
            likes: 0,
            
            dislikes: 0,
            
            comments: []           
        },
        
        {
            id: _.uniqueId(),
            reply: null,
            authorId: 4,
            authorDisplayName: 'Yetanother User',
            authorPhoto: 'http://placehold.it/64x64',
            content: "Post 5. Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            displayTime: "10:08 AM 7 August, 2013",
            createdTs: 1376577740,
            likes: 0,
            
            dislikes: 0,
            
            comments: []           
        },
        
        {
            id: _.uniqueId(),
            reply: null,
            authorId: 4,
            authorDisplayName: 'Yetanother User',
            authorPhoto: 'http://placehold.it/64x64',
            content: "Post 6. Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            displayTime: "10:05 AM 7 August, 2013",
            createdTs: 1376577730,
            likes: 0,
            
            dislikes: 0,
            
            comments: []           
        },
        
        {
            id: _.uniqueId(),
            reply: null,
            authorId: 4,
            authorDisplayName: 'Yetanother User',
            authorPhoto: 'http://placehold.it/64x64',
            content: "Post 7. Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            displayTime: "10:00 AM 7 August, 2013",
            createdTs: 1376577720,
            likes: 0,
            
            dislikes: 0,
            
            comments: []           
        }
    ]);
    
    fauxServer.get('api/posts', function(context) {
        var responseObj,
            postsToReturn,
            filterUserId = context.data.filters.usersRecordsOnly || false,
            filteredPosts = fixturePosts;
        
        if(filterUserId) {
            filteredPosts = fixturePosts.where({authorId: filterUserId});
        }
        
        postsToReturn = new Backbone.Collection(filteredPosts.slice(context.data.offset, context.data.offset + context.data.limit));
        
        responseObj = {
            posts: postsToReturn.toJSON(),
            totalCount: filteredPosts.length
        };
        
        return responseObj;
    });
    
    fauxServer.addRoute('createPost', 'api/posts', 'POST', function(context) {
       var time = new Date(),
           ts = Math.round(time.getTime() / 1000);
       
       context.data.id = _.uniqueId();
       context.data = _.extend(context.data, { 
           authorId: webUser.id,
           authorDisplayName: webUser.displayName,
           authorPhoto: 'http://placehold.it/64x64',
           displayTime: $.format.date(time, 'hh:mm a dd MMMM, yyyy'),
           createdTs: ts
       });
       fixturePosts.push(context.data);
       return context.data;
    });
    
    fauxServer.addRoute('deletePost', 'api/posts/:id', 'DELETE', function(context) {
       fixturePosts.remove(context.data);
       return context.data;
    });
    
    fauxServer.addRoute('updatePost', 'api/posts/:id', 'PUT', function(context) {
       var time = new Date(),
           ts = Math.round(time.getTime() / 1000);
           
       context.data.editedTs = ts;
       fixturePosts.set(context.data);
       return context.data;
    });
    
    PostsApp.Feed.posts.limit = 3;
    
    PostsApp.start();
});
