/** 
 * Invitation candidates module.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Candidates.Invite', function(Invite, App, Backbone, Marionette, $, _) {
    
    // prevent starting with parent
    this.startWithParent = false;    
    
    var Candidates = App.module('Candidates');
    
    var UserView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#user-item-tpl',

        ui: {
            controls: 'span.controls',
            inviteBtn: 'span.controls > small'
        },
                
        triggers: {
            'mouseenter': 'mouseEnter',
            'mouseleave': 'mouseLeave',
            'click span.controls > small': 'inviteBtnClicked'
        },
        
        modelEvents: {
            'change:invited': 'render'
        },
        
        onMouseEnter: function() {
            if(!Candidates.canInvite())
                return;
    
            if(this.model.get('invited') === false)
                this.ui.controls.show();
        },
                
        onMouseLeave: function() {
            this.ui.controls.hide();
        },
                
        onInviteBtnClicked: function() {
            this.$el.mask();
            Candidates.cands.create(
                {
                    user_id: this.model.get('user_id')
                }, 
                {
                    wait: true,
                    success: _.bind(function() {
                        this.model.set('invited', true);
                        this.$el.unmask();
                    }, this)
                }
            );
        }
    });    
    
    var UsersListView = Candidates.FeedView.extend({
        itemView: UserView
    });
    
    var Users = FeedCollection.extend({
        limit: 20,
        model: Candidates.User,
        url: UrlManager.createUrlCallback('api/profile')
    });    
    
    this.addInitializer(function() {
        
        this.users = new Users();
        this.usersList = new UsersListView({
            collection: this.users
        });
        
        this.users.on('add', function(mod, col) {
            if(Candidates.cands.find(function(item){
                    return item.get('profile').user_id == mod.get('user_id');
                })
            )
                mod.set('invited', true);
            else
                mod.set('invited', false);
        });
        
        Candidates.cands.on('remove', function(mod, col) {
            var user = Candidates.Invite.users.findWhere({user_id: parseInt(mod.get('profile').user_id)});
            
            if(user)
                user.set('invited', false);
        });
            
        Candidates.cands.on('add', function(mod, col) {
            var user = Candidates.Invite.users.findWhere({user_id: parseInt(mod.get('profile').user_id)});
            
            if(user)
                user.set('invited', true);
        });
    });
    
});


