/** 
 * AdminsManagement module. Lists active admins, allows to invite other
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('AdminsManagement', function(AdminsManagement, App, Backbone, Marionette, $, _) {
    
    var config = {
        electionId: null,
        canDeprive: false,
        canEmpover: false
    };
    
    var AdminsLayout = Marionette.Layout.extend({
        template: '#admins-layout-tpl',
        regions: {
            adminsList: '#all-admins-tab',
            invite: '#invite-tab'
        }
    });
    
    var NoItemView = Marionette.ItemView.extend({
        template: '#no-item-tpl'
    });
    
    var AdminItemView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#admin-item-tpl',
        
        ui: {
            controls: 'span.controls',
            depriveBtn: 'span.controls > small'
        },
                
        triggers: {
            'mouseenter': 'mouseEnter',
            'mouseleave': 'mouseLeave',
            'click span.controls > small': 'depriveBtnClick'
        },
        
        onMouseEnter: function() {
            if(!AdminsManagement.canDeprive())
                return;
            
            if(this.model.get('authAssignment').itemname !== 'election_creator')
                this.ui.controls.show();
        },
                
        onMouseLeave: function() {
            this.ui.controls.hide();
        },
                
        onDepriveBtnClick: function() {
            this.$el.mask();
            this.model.destroy({
                wait: true
            });
        }
    });
    
    var FeedView = Marionette.CompositeView.extend({
        template: '#admins-list-tpl',
        itemViewContainer: 'div.items',
        itemView: UserView,
        emptyView: NoItemView,
        
        ui: {
            itemsCounter: 'span.items-count',
            filter: 'input[name="userName"]',
            filterBtn: 'button.userName-filter-apply',
            filterResetBtn: 'button.filter-reset',
            loader: 'img.loader'
        },
        
        events: {
            'click button.userName-filter-apply': 'onFilterBtnClicked',
            'click button.filter-reset': 'onResetBtnClicked'
        },
        
        onFilterBtnClicked: function(e) {
            e.preventDefault();
    
            var value = this.ui.filter.val();
            if(!value)
                return;
            
            this.collection.setFilter('name', value);
        },
        
        onResetBtnClicked: function(e) {
            e.preventDefault();
            
            this.ui.filter.val('');
            this.collection.setFilter('name', '');
            this.collection.reset();
        },
        
        initialize: function() {
            this.listenTo(this.collection, 'totalCountChanged', _.bind(function(actualValue) {
                this.ui.itemsCounter.html(actualValue);
            }, this));
            
            this.listenTo(this.collection, 'request', function() {
                this.$el.mask();
                this.ui.loader.show();
            });

            this.listenTo(this.collection, 'sync remove add', _.bind(function(collection) {
                this.$el.unmask();
                this.ui.loader.hide();
            }, this));
            
            this.moreBtnView = new MoreView({
                view: this,
                appendTo: _.bind(function() { return $('div.load-btn-cntr', this.$el);}, this)
            });
        }
    });
    
    var AdminsListView = FeedView.extend({
        itemView: AdminItemView
    });
    
    var User = Backbone.Model.extend({
        
        defaults: {
            empovered: false
        },
        
        parse: function() {
            var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

            attrs.birth_day = parseInt(attrs.birth_day) * 1000;
            attrs.user_id = parseInt(attrs.user_id);

            return attrs;
        },

        toJSON: function(options) {
            var json = Backbone.Model.prototype.toJSON.call(this);
            
            if(options && _.has(options, 'success')) {
                json.birth_day = this.get('birth_day').toString().substr(0, 10);
            }
            
            return json;
        }
    });
    
    var Admin = Backbone.Model.extend({
        
        parse: function() {
            var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

            attrs.id = parseInt(attrs.id);
            attrs.object_id = parseInt(attrs.object_id);
            attrs.profile.birth_day = parseInt(attrs.profile.birth_day) * 1000;
            attrs.profile.user_id = parseInt(attrs.profile.user_id);

            return attrs;
        },

        toJSON: function(options) {
            var json = Backbone.Model.prototype.toJSON.call(this);
            
            if(options && _.has(options, 'success')) {
                json.profile.birth_day = this.get('profile').birth_day.toString().substr(0, 10);
            }
            
            return json;
        }
    });
    
    var Admins = FeedCollection.extend({
        limit: 20,
        model: Admin,
        url: UrlManager.createUrlCallback('api/electionAdmin'),
        
        electionId: null,
        
        create: function(model, options) {
            if(model instanceof User) {
                model.set('object_id', this.electionId);
            }else
                model.object_id = this.electionId;
            
            FeedCollection.prototype.create.apply(this, arguments);
        },
                
        setElectionId: function(value) {
            this.electionId = value;
            this.filters.election_id = value;
        }
    });
    
    /**
     * Invite tab
     */
    
    var UserView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#user-item-tpl',

        ui: {
            controls: 'span.controls',
            empoverBtn: 'span.controls > small'
        },
                
        triggers: {
            'mouseenter': 'mouseEnter',
            'mouseleave': 'mouseLeave',
            'click span.controls > small': 'empoverBtnClicked'
        },
        
        modelEvents: {
            'change:empovered': 'render'
        },
        
        onMouseEnter: function() {
            if(!AdminsManagement.canEmpover())
                return;
    
            if(this.model.get('empovered') === false)
                this.ui.controls.show();
        },
                
        onMouseLeave: function() {
            this.ui.controls.hide();
        },
                
        onEmpoverBtnClicked: function() {
            this.$el.mask();
            AdminsManagement.admins.create(
                {
                    profile: _.clone(this.model.attributes)
                } , 
                {
                    wait: true,
                    success: _.bind(function() {
                        this.model.set('empovered', true);
                        this.$el.unmask();
                    }, this)
                }
            );
        }
    });
    
    var UsersListView = FeedView.extend({
        itemView: UserView
    });
    
    var Users = FeedCollection.extend({
        limit: 20,
        model: User,
        url: UrlManager.createUrlCallback('api/profile')
    });
    
    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };
    
    this.canDeprive = function() {
        return config.canDeprive;
    };
    
    this.canEmpover = function() {
        return config.canEmpover;
    };
    
    this.addInitializer(function() {
        this.admins = new Admins();
        
        this.layout = new AdminsLayout();
        this.adminsList = new AdminsListView({
            collection: this.admins
        });
        
        this.users = new Users();
        this.usersList = new UsersListView({
            collection: this.users
        });
        
        this.users.on('add', function(mod, col) {
            if(AdminsManagement.admins.find(function(item){
                    return item.get('profile').user_id == mod.get('user_id');
                })
            )
                mod.set('empovered', true);
            else
                mod.set('empovered', false);
        });
        
        this.admins.on('remove', function(mod, col) {
            var user = AdminsManagement.users.findWhere({user_id: parseInt(mod.get('profile').user_id)});
            
            if(user)
                user.set('empovered', false);
        });
        
        this.layout.on('show', function() {
            this.adminsList.show(AdminsManagement.adminsList);
           
            if(AdminsManagement.canEmpover()) {
                $('#invite-tab-sel').show();
                this.invite.show(AdminsManagement.usersList);
            }
        });        
    });
    
    App.on('start', function() {
        AdminsManagement.admins.setElectionId(config.electionId);
        AdminsManagement.admins.fetch({success: function() {
            
            if(AdminsManagement.canEmpover())
                AdminsManagement.users.fetch();    
            
        }});
    });
});


