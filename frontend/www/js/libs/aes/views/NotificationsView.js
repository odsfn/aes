/** 
 * Shows notifications to user
 */
var Aes = Aes || {};

Aes.NotificationView = Aes.ItemView.extend({
    
    events: {
        'click a.close': 'close'
    },
    
    onClose: function() {
        this.model.destroy();
    },
    
    attributes: function() {
        var typeCls = '';
        var availTypes = ['success','info','error'];
        var type = this.model.get('type');
        
        if(type && availTypes.indexOf(type) !== -1) {
            typeCls = ' alert-' + type;
        }
        
        return {
            class: 'alert in alert-block fade' + typeCls
        };
    },
    
    getTplStr: function() {
        return Aes.NotificationView.getTpl();
    }
},{
    getTpl: function() {
        return '<a href="#" class="close" data-dismiss="alert">×</a><%= body %>';
    }
});

Aes.NotificationsView = Marionette.CollectionView.extend({
    itemView: Aes.NotificationView,
    
    topContainerSel: 'div.flash-messages',
    
    notify: function() {
        
        var config = arguments[0];
        
        if(typeof(config) === 'string') {
            config = {
                body: config
            };
            
            if(arguments.length > 1) {
                config.type = arguments[1];
            }
        }
        
        this.collection.add(new Backbone.Model(config));
    },
    
    initialize: function(options) {
        this.collection = new Backbone.Collection([]);
        
        this.collection.on('add remove', function(model, collection, opts) {
            if (collection.length > 0)
                $(this.topContainerSel).show();
            else
                $(this.topContainerSel).hide();
        }, this);
    }
});

Aes.Notifications = (function() {
    var notificationsView = new Aes.NotificationsView(),
        $cntr = 'div.flash-messages > div > div',
        
        collectMessagesFromHtml = function() {
            var messages = [];
            
            $cntr.find('div').each(function(index, el) {
                var type = '',
                    $el = $(el);

                if ($el.hasClass('alert-success'))
                    type = 'success';
                else if ($el.hasClass('alert-info'))
                    type = 'info';
                else if ($el.hasClass('alert-error'))
                    type = 'error';

                //remove close btn
                $el.find('a.close').remove();

                messages.push({
                    body: $el.html(),
                    type: type
                });
            });
            
            $cntr.html('');
            
            return messages;
        };
    
    $(function() {
        $cntr = $($cntr);
        var messages = collectMessagesFromHtml();
        notificationsView.setElement($cntr);
        if(messages.length)
            notificationsView.collection.add(messages);
    });
    
    return {
        add: function() {
            notificationsView.notify.apply(notificationsView, arguments);
        }
    };
})();
