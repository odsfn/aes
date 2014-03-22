/**
 * Lists users nominations, provides ability to accept or decline it
 *   
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Nominations', function(Nominations, App, Backbone, Marionette, $, _) {
    
    var config = {
        userId: null,
        layoutTpl: '#nominations-layout',
        canControl: false
    };
    
    var Nomination = Candidate.extend({
        
        urlRoot: UrlManager.createUrlCallback('api/nomination'),
        
        _election: null,
        
        getElection: function() {
            if(!this._election)
                this._election = new Election(this.get('election'));
            
            return this._election;
        }
        
    });
    
    var NominationsCollection = FeedCollection.extend({
        limit: 30,
        model: Nomination,
        url: UrlManager.createUrlCallback('api/nomination'),
        userId: null,
        
        setUserId: function(value) {
            this.userId = value;
            this.filter.user_id = value;
        }        
    });
    
    var NominationView = Aes.ItemView.extend({
        template: '#nomination-tpl',
        
        ui: {
            controls: '.controls',
            rates: '.rates-container',
            comments: '.comments-container',
            acceptBtn: '.accept-btn',
            declineBtn: '.decline-btn'
        },
        
        events: {
            'click .accept-btn': 'onAcceptClick',
            'click .decline-btn': 'onDeclineClick'
        },
                
        modelEvents: {
            'change:status': "render"
        },
                
        getStatusClass: function() {
            var status = this.model.getElection().getStatusText();
            
            return 'status-' + status.toLowerCase();
        },
        
        serializeData: function() {
            return _.extend(
                   Aes.ItemView.prototype.serializeData.apply(this, arguments),
                   {
                       statusText: this.model.getStatusText()
                   }
            );
        },
         
        canControl: function() {
            return (Nominations.canControl() && this.model.getElection().checkStatus('Registration'));
        },        
                
        onRender: function() {
            this.$el.removeClass();
            this.$el.addClass(this.getStatusClass());
            
            if(!this.canControl() || this.model.checkStatus('Refused') || this.model.checkStatus('Blocked'))
                this.ui.controls.hide();
            else
            {
                if(this.model.checkStatus('Registered'))
                    this.ui.acceptBtn.hide();
            }
            
            if(!this._rates)
            {
                this._rates = RatesWidget.create({
                    targetId: this.model.get('id'),
                    targetType: 'Candidate',
                    targetEl: this.ui.rates
                });
            }
            
            this.ui.rates.prepend(this._rates.render().$el);
            
            if(!this._comments)
            {
                this._comments = CommentsWidget.create({
                    targetId: this.model.get('id'),
                    targetType: 'Candidate'
                });
            }
            
            this.ui.comments.prepend(this._comments.render().$el);
        },
          
        onShow: function() {
            this._rates.trigger('show');
            this._comments.trigger('show');
        },
                
        onAcceptClick: function() {
            if(!this.canControl())
                return;
            
            this.ui.controls.mask();
            
            this.model.save({
                status: this.model.getStatusId('Registered')
            }, {
                wait: true
            }).done(_.bind(function() {
                this.ui.controls.unmask();
            }, this));
        },
                
        onDeclineClick: function() {
            if(!this.canControl())
                return;
            
            this.model.save({
                status: this.model.getStatusId('Refused')
            }, {
                wait: true
            }).done(_.bind(function() {
                this.ui.controls.unmask();
            }, this));
        }
    });
    
    var NominationsFeedView = Aes.FeedView.extend({
        template: '#nominations-feed-tpl',
        itemView: NominationView,
        
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
            
            Aes.FeedView.prototype.initialize.apply(this, arguments);
    
            _.extend(this.ui, {
                filter: 'input[name="name"]',
                filterBtn: 'button.filter-apply',
                filterResetBtn: 'button.filter-reset',
            });
            
            if(!this.events)
                this.events = {};
            
            _.extend(this.events, {
                'click button.filter-apply': 'onFilterBtnClicked',
                'click button.filter-reset': 'onResetBtnClicked'
            });
        }
    });
    
    var Layout = Marionette.Layout.extend({
       regions: {
           nominations: '#nominations-feed-container'
       } 
    });
    
    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };
    
    this.canControl = function() {
        return config.canControl;
    };
    
    this.addInitializer(function(options) {
        
        this.setOptions(options);
        
        this.nominations = new NominationsCollection();
        this.nominations.setUserId(config.userId);
        
        this.nominationsFeedView = new NominationsFeedView({
            collection: this.nominations
        });
        
        this.layout = new Layout({
           template: config.layoutTpl
        });
        
    });
    
    this.on('start', function() {
        
        this.nominations.fetch().done(function(){
           Nominations.layout.nominations.show(Nominations.nominationsFeedView); 
        });
        
    });
    
});

