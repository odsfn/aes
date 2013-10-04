<?php 

Yii::app()->clientScript->reset();
$this->layout = '//layouts/empty';

?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="/assets/b021ef6c/jquery.js"></script>
<script type="text/javascript" src="/assets/5d47a05f/js/bootstrap.js"></script>
<script type="text/javascript" src="/assets/5d47a05f/js/bootstrap.bootbox.min.js"></script>
<script type="text/javascript" src="/assets/5d47a05f/js/bootstrap.notify.js"></script>
<script type="text/javascript" src="/assets/b021ef6c/jui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/libs/backbone.marionette/json2.js"></script>
<script type="text/javascript" src="/js/libs/backbone.marionette/underscore.js"></script>
<script type="text/javascript" src="/js/libs/backbone.marionette/backbone.js"></script>
<script type="text/javascript" src="/js/libs/backbone.marionette/backbone.marionette.js"></script>
<script type="text/javascript" src="/js/libs/aes/helpers.js"></script>
<script type="text/javascript" src="/js/libs/aes/WebUser.js"></script>
<script type="text/javascript" src="/js/libs/aes/i18n.js"></script>
<script type="text/javascript" src="/js/libs/loadmask/loadmask.js"></script>
<script type="text/javascript" src="/js/libs/aes/collections/FeedCollection.js"></script>
<script type="text/javascript" src="/js/libs/aes/views/MoreView.js"></script>
<script type="text/javascript" src="/js/libs/aes/views/FeedCountView.js"></script>
<script type="text/javascript" src="/assets/812a9edd/MessagingApp.js"></script>
<script type="text/javascript" src="/assets/812a9edd/models/Conversation.js"></script>
<script type="text/javascript" src="/assets/812a9edd/models/Message.js"></script>
<script type="text/javascript" src="/assets/812a9edd/collections/Conversations.js"></script>
<script type="text/javascript" src="/assets/812a9edd/collections/Messages.js"></script>
<script type="text/javascript" src="/assets/812a9edd/modules/Messaging.js"></script>
<script type="text/javascript" src="/assets/812a9edd/modules/Chat.js"></script>
<script type="text/javascript" src="/assets/812a9edd/dev/app.dev.js"></script>
<script type="text/javascript" src="/js/libs/jquery.dateFormat-1.0.js"></script>
<script type="text/javascript" src="/js/libs/backbone-faux-server.js"></script>        

<script type="text/javascript">
    var Router = Marionette.AppRouter.extend({
        routes: {
            "foo": "foo"
        },

        appRoutes: {
            "chat_with/:userId": "startChat"
        },
        
        foo: function() {
            console.log('Foo routed');
        }         
    });
    
    $(function() {
        $('div.nv a').click(function(e) {
            e.preventDefault();
            console.log('clicked');
            router.navigate($(this).attr('href'), {trigger: true});
        });
    
        var script = "<?php if(preg_match('/index-test\.php/', $_SERVER['SCRIPT_FILENAME'])) echo 'index-test.php'; ?>";
        var router = new Router({
            controller: {
                startChat: function(userId) {
                    console.log('startChat called with ' + userId);
                }
            }
        });
        
        Backbone.history.start({
            pushState: true,
            root: script + '/messaging/index'
        });
    });
</script>

    </head>
    <body>
        <div>TODO write content</div>
        <div class="nv">
            <a href="foo">Foo</a>, <a href="chat_with/1">Chat with 1</a>, <a href="chat_with/2">Chat with 2</a>
        </div>
        <script type="text/javascript">
            document.write((new Date()).getTime());
        </script>
    </body>
</html>
