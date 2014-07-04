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
    }
});

Aes.Notifications = (function() {
    var notificationsView = new Aes.NotificationsView();
    
    $(function() {
        notificationsView.setElement($('div.flash-messages > div > div'));
    });
    
    return {
        add: function() {
            notificationsView.notify.apply(notificationsView, arguments);
        }
    };
})();
