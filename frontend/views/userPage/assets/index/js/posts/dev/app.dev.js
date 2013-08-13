/* 
 * Isolated version from server
 */
$(function(){
    console.log('In app.dev.js');
    console.log("fauxServer version: " + fauxServer.getVersion());
    
    var fixturePosts = new Backbone.Collection([
        {
            id: 133,
            reply: null,
            author: {
                id: 1,
                displayName: 'Vasiliy Pedak',
                photo: 'http://placehold.it/64x64'
            },
            
            content: "When creating a Collection, you may choose to pass in the initial array of models. The collection's comparator may be included as an option. Passing false as the comparator option will prevent sorting. If you define an initialize function, it will be invoked when the collection is created.",
            
            displayTime: "10:42 AM 8 August, 2013",
            
            likes: 165,
            
            dislikes: 32,
            
            comments: []
        },
        
        {
            id: 231,
            reply: null,
            author: {
                id: 1,
                displayName: 'Vasiliy Pedak',
                photo: 'http://placehold.it/64x64'
            },
            
            content: "Lorem ipsum dolor sit amet, at debet dolores est, oratio omnium iisque ut vel. Eam stet reque nulla cu. Patrioque persecuti interpretaris ut usu, docendi senserit sea no. Vel tota interpretaris an.",
            
            displayTime: "7:13 PM 8 August, 2013",
            
            likes: 165,
            
            dislikes: 32,
            
            comments: [
                {
                    id: 516,
                    reply: 231,
                    author: {
                        id: 2,
                        displayName: 'Another User',
                        photo: "http://placehold.it/64x64"
                    },
                    content: "Lorem ipsum dolor sit amet, at debet dolores est.",
                    displayTime: "7:46PM 8 August, 2013",
                    likes: 5,
                    dislikes: 2,                    
                },
                
                {
                    id: 518,
                    reply: 516,
                    author: {
                        id: 2,
                        displayName: 'Vasiliy Pedak',
                        photo: "http://placehold.it/64x64"
                    },
                    content: "At debet dolores est. Lorem ipsum dolor sit amet",
                    displayTime: "10:11PM 8 August, 2013",
                    likes: 0,
                    dislikes: 0,                      
                }
            ]
        },
        
        {
            id: 333,
            reply: null,
            author: {
                id: 3,
                displayName: 'Jhon Lenon',
                photo: 'http://placehold.it/64x64'
            },
            
            content: "The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.",
            
            displayTime: "11:42 AM 14 August, 2013",
            
            likes: 0,
            
            dislikes: 0,
            
            comments: []
        },
    ]);
    
    fauxServer.get('api/posts', function(context) {
        return fixturePosts.toJSON();
    });
    
    PostsApp.start();
});
