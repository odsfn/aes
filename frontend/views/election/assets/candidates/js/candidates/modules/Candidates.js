/** 
 * Candidates module.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Candidates', function(Candidates, App, Backbone, Marionette, $, _) {
    
    var config = {
        electionId: null,
        canDeprive: false,
        canInvite: false,
        canVote: false
    };
    
    var CandidatesLayout = Marionette.Layout.extend({
        template: '#cands-layout-tpl',
        regions: {
            electoralList: '#electoral-list-tab',
            candsList: '#all-cands-tab',
            invite: '#invite-tab'
        }
    });
    
    var NoItemView = Marionette.ItemView.extend({
        template: '#no-item-tpl'
    });
    
    var ElectoralCandView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#electoral-list-item-tpl',
        
        _voted: false,
        
        _voteBoxActive: true,
        
        _voteDeclined: false,
        
        activeVote: null,
        
        ui: {
            voteBox: '.checkbox',
            voteBoxValue: '.checkbox .value'
        },
        
        triggers: {
            'click .checkbox': 'voteBoxClicked'
        },
        
        onVoteBoxClicked: function() {
            if(!this._voteBoxActive)
                return;
            
            var voted = !this._voted;
            
            this.$el.mask();
            
            if(voted) {
                Candidates.votes.create({
                    user_id: WebUser.getId(),
                    candidate_id: this.model.get('id')
                }, {
                    wait: true,
                    success: _.bind(function(model) {
                        this.ui.voteBoxValue.html('&#10003;');
                        this.trigger('voteAdded');
                        this._voted = voted;  
                        this.$el.unmask();
                        this.activeVote = model;
                    }, this)
                });
            }
            else {
                this.activeVote.destroy({
                    wait: true,
                    success: _.bind(function(){
                        this.ui.voteBoxValue.html('');
                        this.trigger('voteDeleted');
                        this._voted = voted;
                        this.$el.unmask();
                        this.activeVote = null;
                    }, this)
                })
            }
        },
        
        activateVoteBox: function() {
        
            if(this._voteBoxActive || this._voteDeclined)
                return;
    
            this._voteBoxActive = true;
            this.ui.voteBox.removeClass('inactive');
        },
        
        inactivateVoteBox: function() {
    
            if(!this._voteBoxActive)
                return;
    
            this._voteBoxActive = false;
            this.ui.voteBox.addClass('inactive');    
        },
        
        serializeData: function() {
            return _.extend(Marionette.ItemView.prototype.serializeData.call(this), {
                view: this,
                statusText: this.model.getStatusText()
            });
        },
                
        initialize: function() {
            var candidateVote = Candidates.votes.findWhere({candidate_id: this.model.get('id')});
            
            this.activeVote = candidateVote;
            
            if(candidateVote) {
                var voteStatus = candidateVote.get('status');
                
                if(voteStatus === 0)
                    this._voted = true;
                else if(voteStatus === 1) {
                    this._voted = true;
                    this._voteBoxActive = false;
                } else {
                    this._voteDeclined = true;
                    this._voteBoxActive = false;
                }
            } else {
                var voted = Candidates.votes.findWhere({status: 0}) || Candidates.votes.findWhere({status: 1});
                
                if(voted)
                    this._voteBoxActive = false;
            }
            
            if(!Candidates.canVote())
                this._voteBoxActive = false;
        }
    });
    
    var CandItemView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#cand-list-item-tpl',
        
        ui: {
            controls: 'span.controls',
            depriveBtn: 'span.controls > small'
        },
                
        triggers: {
            'mouseenter': 'mouseEnter',
            'mouseleave': 'mouseLeave'
//            'click span.controls > small': 'depriveBtnClick'
        },
        
        onMouseEnter: function() {
//            if(!Candidates.canDeprive())
//                return;
//            
//            if(this.model.get('authAssignment').itemname !== 'election_creator')
//                this.ui.controls.show();
        },
                
        onMouseLeave: function() {
//            this.ui.controls.hide();
        },
                
        onDepriveBtnClick: function() {
            this.$el.mask();
            this.model.destroy({
                wait: true
            });
        },
                
        serializeData: function() {
            return _.extend(Marionette.ItemView.prototype.serializeData.call(this), {
                statusText: this.model.getStatusText()
            });
        }
    });
    
    Candidates.FeedView = Marionette.CompositeView.extend({
        template: '#cands-list-tpl',
        itemViewContainer: 'div.items',
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
    
    var ElectoralList = Candidates.FeedView.extend({
        
        itemView: ElectoralCandView,
        
        initialize: function() {
            
            this.on('itemview:voteAdded', function(votedItemView) {
               this.children.each(function(itemView){
                   if(itemView !== votedItemView)
                       itemView.inactivateVoteBox();
               }); 
            });
            
            this.on('itemview:voteDeleted', function(votedItemView) {
                this.children.each(function(itemView){
                    itemView.activateVoteBox();
                });
            });
            
            Candidates.FeedView.prototype.initialize.apply(this, arguments);
        }
        
    });
    
    var CandsListView = Candidates.FeedView.extend({
        itemView: CandItemView
    });
    
    Candidates.User = Backbone.Model.extend({
        
        defaults: {
            invited: false
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
    
    var Candidate = Backbone.Model.extend({
        
        parse: function() {
            var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

            attrs.id = parseInt(attrs.id);
            attrs.user_id = parseInt(attrs.user_id);
            attrs.election_id = parseInt(attrs.election_id);
            attrs.profile.birth_day = parseInt(attrs.profile.birth_day) * 1000;
            attrs.profile.user_id = parseInt(attrs.profile.user_id);

            return attrs;
        },

        toJSON: function(options) {
            var json = Backbone.Model.prototype.toJSON.call(this);
            
            return json;
        },
                
        getStatusText: function() {
            return Candidate.getStatuses()[this.get('status')];
        }
        
    }, {
        getStatuses: function() {
            return ['Invited', 'Awaiting registration confirmation', 'Registered'];
        }
    });
    
    var Cands = FeedCollection.extend({
        limit: 20,
        model: Candidate,
        url: UrlManager.createUrlCallback('api/candidate'),
        
        electionId: null,
        
        create: function(model, options) {
            if(model instanceof Candidates.User) {
                model.set('election_id', this.electionId);
            }else
                model.election_id = this.electionId;
            
            FeedCollection.prototype.create.apply(this, arguments);
        },
                
        setElectionId: function(value) {
            this.electionId = value;
            this.filter.election_id = value;
        }
    });
    
    var Vote = Backbone.Model.extend({
        parse: function() {
            var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

            attrs.id = parseInt(attrs.id);
            attrs.election_id = parseInt(attrs.election_id);
            attrs.candidate_id = parseInt(attrs.candidate_id);
            attrs.user_id = parseInt(attrs.user_id);
            attrs.date = parseInt(attrs.date) * 1000;
            attrs.status = parseInt(attrs.status);

            return attrs;
        }
    });
    
    var Votes = FeedCollection.extend({
       limit: 2000,
       model: Vote,
       url: UrlManager.createUrlCallback('api/vote'),
       
       electionId: null,
       
       setElectionId: function(value) {
        this.electionId = value;
        this.filter.election_id = value;
       }
    });
    
    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };
    
    this.canDeprive = function() {
        return config.canDeprive;
    };
    
    this.canInvite = function() {
        return config.canInvite;
    };
    
    this.canVote = function() {
        return config.canVote;
    };
    
    this.addInitializer(function() {
        this.cands = new Cands();
        
        this.approvedCands = new Cands();
        this.approvedCands.filter.status = 2;
        
        this.votes = new Votes();
        
        this.layout = new CandidatesLayout();
        
        this.electoralList = new ElectoralList({
            collection: this.approvedCands
        });
        
        this.candsList = new CandsListView({
            collection: this.cands
        });
        
        this.layout.on('show', function() {
            
            this.electoralList.show(Candidates.electoralList);
            
            this.candsList.show(Candidates.candsList);
           
            if(Candidates.canInvite()) {
                $('#invite-tab-sel').show();
            }
        });        
    });
    
    App.on('start', function() {
        Candidates.cands.setElectionId(config.electionId);
        Candidates.votes.setElectionId(config.electionId);
        Candidates.votes.filter.user_id = WebUser.getId() || 0;
        
        Candidates.approvedCands.setElectionId(config.electionId);
        Candidates.votes.fetch({success: function(){
            Candidates.approvedCands.fetch();
        }});
        
        if(Candidates.canInvite()) {
            Candidates.Invite.start();
            Candidates.layout.invite.show(Candidates.Invite.usersList);
        }
        
        Candidates.cands.fetch({success: function() {
            
            if(Candidates.canInvite())
                Candidates.Invite.users.fetch();
            
        }});
    });
});


