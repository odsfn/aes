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
        canVote: false,
        currentCandidateId: null
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
    
    Candidates.VoteBoxView = Marionette.ItemView.extend({
        
        template: '#vote-box-tpl',
        
        ui: {
            voteBox: '.checkbox',
            voteBoxValue: '.checkbox .value'
        },
        
        triggers: {
            'click .checkbox': 'voteBoxClicked'
        },
        
        modelEvents: {
            'change': 'render'
        },
        
        onVoteBoxClicked: function() {
            if(!this.model.canVote())
                return;
            
            var voted = !this.model.get('voted');
            
            this.$el.mask();
            
            if(voted) {
                
                this.model.passVote(_.bind(function(){
                    this.ui.voteBoxValue.html('&#10003;');
                    this.trigger('voteAdded');
                    this._voted = voted;  
                    this.$el.unmask();
                }, this));
                
            } else {

                this.model.declineVote(_.bind(function(){
                    this.ui.voteBoxValue.html('');
                    this.trigger('voteDeleted');
                    this._voted = voted;
                    this.$el.unmask();
                }, this));
                
            }
        },
       
        initialize: function() {

            if(!Candidates.canVote()) {
                this.model.set('active', false);
                return;
            }
            
        }       
    });
    
    var ElectoralCandView = Candidates.ElectoralCandView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#electoral-list-item-tpl',
        
        ui: {
            voteBoxCntr: 'div.vote-cntr'
        },
        
        serializeData: function() {
            return _.extend(Marionette.ItemView.prototype.serializeData.call(this), {
                statusText: this.model.getStatusText()
            });
        },
                
        onRender: function() {
            this._voteBoxView.render();
        },
                
        onShow: function() {
            this.ui.voteBoxCntr.append(this._voteBoxView.$el);
        },
                
        initialize: function() {

            var model, candId = this.model.get('id');
            
            model = Candidates.voteBoxModels.findWhere({candidate_id: candId});

            if(!model) {
                model = new Candidates.VoteBoxModel({
                    candidate_id: candId
                });
                
                Candidates.voteBoxModels.add(model);
            }
            
            var candidateVote = Candidates.votes.findWhere({candidate_id: this.model.get('id')});
            
            if(candidateVote)
                model.set('vote', candidateVote);
            
            this._voteBoxView = new Candidates.VoteBoxView({model: model });
        }
    });
    
    var CandItemView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#cand-list-item-tpl',
                
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
        itemView: ElectoralCandView
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
    
    Candidates.Vote = Backbone.Model.extend({
        parse: function() {
            var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

            attrs.id = parseInt(attrs.id);
            attrs.election_id = parseInt(attrs.election_id);
            attrs.candidate_id = parseInt(attrs.candidate_id);
            attrs.user_id = parseInt(attrs.user_id);
            attrs.date = parseInt(attrs.date) * 1000;
            attrs.status = parseInt(attrs.status);

            return attrs;
        },
                
        isVoted: function() {
            return (this.get('user_id') && this.get('candidate_id'));
        },
         
        isDeclined: function() {
            return (this.get('status') === 1);
        },
                
        canVote: function() {
            var passedVote = Candidates.votes.getPassedVote();
            
            return (!passedVote || _.isEqual(passedVote, this));
        }
    });
    
    Candidates.VoteBoxModel = Backbone.Model.extend({
        defaults: {
            voted: false,
            active: false,
            declined: false,
            vote: null,
            
            candidate_id: null
        },
                
        passVote: function(success) {
            Candidates.votes.create(
                {
                    user_id: WebUser.getId(),
                    candidate_id: this.get('candidate_id')
                }, 
                {
                    wait: true,
                    success: _.bind(function(model) {
                        this.set('vote', model);
                        success();
                    }, this)
                }
            );    
        },
                
        declineVote: function(success) {
            var vote = this.get('vote');
            
            if(!vote)
                return;
            
            vote.destroy({
                wait: true,
                success: success
            });
        },
                
        updateAttrs: function() {
    
            var vote = this.get('vote');
            
            if(vote) {
                var declined = vote.isDeclined();
                
                this.set({
                   voted: true,
                   active:  (!declined && vote.canVote()),
                   declined: declined
                });                
            } else {
                var hasPassedVote = false;
                
                if(Candidates.votes.getPassedVote())
                    hasPassedVote = true;
                
                this.set({
                    voted: false,
                    active: !hasPassedVote,
                    declined: false
                });
            }
        },        
             
        canVote: function() {
            return this.get('active');
        },
                
        initialize: function() {
            this.on('change:vote', _.bind(function(m, v, o) {
                
                var vote = this.get('vote'); 
                
                if(vote) {
                    
                    this.listenTo(vote, 'destroy', function() {
                        this.set({
                            voted: false,
                            active: true,
                            declined: false,
                            vote: null
                        });
                    });
                }
                
                this.updateAttrs();
                
            }, this));
            
            this.listenTo(Candidates.votes, 'sync remove', this.updateAttrs);
            
            this.updateAttrs();
        }
    });
    
    Candidates.Votes = FeedCollection.extend({
       limit: 2000,
       model: Candidates.Vote,
       url: UrlManager.createUrlCallback('api/vote'),
       
       electionId: null,
       
       setElectionId: function(value) {
            this.electionId = value;
            this.filter.election_id = value;
       },
               
       /**
        * Returns the vote that had benn passed by current user and had not been declined by candidate 
        * @returns {Vote}
        */
       getPassedVote: function() {
            return this.find(function(m) {
                return ( m.isVoted() && m.get('status') !== 1); 
            });
       }
    });
    
    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };
    
    this.canDeprive = function() {
        return config.canDeprive;
    };
    
    this.canDecline = function() {
        return config.canDecline;
    };
    
    this.canInvite = function() {
        return config.canInvite;
    };
    
    this.canVote = function() {
        return config.canVote;
    };
    
    this.getElectionId = function() {
        return config.electionId;
    };
    
    this.getCurrentCandidateId = function() {
        return config.currentCandidateId;
    };
    
    this.viewDetails = function(candId) {
        
        var cand = this.cands.findWhere({id: parseInt(candId)});
        
        this.Details.viewCandidate(cand);
        
        $('ul.breadcrumbs li').last().find('a').addClass('route').attr('href', '');
        
        $('<li><a>' + cand.get('profile').displayName + '</a></li>').appendTo('ul.breadcrumbs').attr('href', '#');
        
        $('#candidate-details').show();
        $('#candidates').hide();
        
        this._detailsViewing = true;
    };
    
    this.viewCandidates = function() {
        
        if(this._detailsViewing)
            $('ul.breadcrumbs li').last().remove();
        
        $('#candidate-details').hide();
        $('#candidates').show();
        
        this._detailsViewing = false;
    };
    
    this.addInitializer(function() {
        this.cands = new Cands();
        
        this.approvedCands = new Cands();
        this.approvedCands.filter.status = 2;
        
        this.votes = new Candidates.Votes();
        
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
        Candidates.voteBoxModels = new Backbone.Collection();
        
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

            Candidates.Details.start();

            Backbone.history.start({
                pushState: true,
                root: UrlManager.createUrl('election/candidates/' + Candidates.getElectionId())
            });                
                
        }});
    
    });
});


