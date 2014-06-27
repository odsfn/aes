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
    
    /**
     * Current election
     * 
     * @type Election
     */
    var election;
    
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
            if(!this.model.canVote()) {
                return;
            }
            
            var fail = function() {
                alert('Action is unavailable because of timeout');
                Candidates.votes.trigger('refresh:availability');
            };
            
            var revoteAbility = Candidates.getRevoteAbility();
            
            var voted = !this.model.get('voted');
            
            this.$el.mask();
            
            if(voted) {
                
                if(!revoteAbility.isAllowed('pass')) {
                    fail();
                    return;
                }
                
                this.model.passVote(_.bind(function(){
                    this.ui.voteBoxValue.html('&#10003;');
                    this.trigger('voteAdded');
                    this._voted = voted;  
                    this.$el.unmask();
                }, this));
                
            } else {

                if(!revoteAbility.isAllowed('revoke')) {
                    fail();
                    return;
                }

                var decline = _.bind(function(){
                    this.model.declineVote(_.bind(function(){
                        this.ui.voteBoxValue.html('');
                        this.trigger('voteDeleted');
                        this._voted = voted;
                        this.$el.unmask();
                    }, this));
                }, this);
                
                var confirmation = new Aes.ConfirmModalView({
                    label: 'Revoke vote confirmation',
                    body: Backbone.Marionette.Renderer.render('#revoke-vote-message', revoteAbility.toJSON()),
                    onConfirm: decline,
                    onCancel: _.bind(function(){
                        this.$el.unmask();
                    }, this)
                });
                
                confirmation.open();
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
        
        modelEvents: {
            'change': 'render'
        },
        
        ui: {
            voteBoxCntr: 'div.vote-cntr'
        },
        
        serializeData: function() {
            return _.extend(Marionette.ItemView.prototype.serializeData.call(this), {
                statusText: this.model.getStatusText()
            });
        },
                
        onRender: function() {
            if( this._voteBoxView ) {
                this._voteBoxView.render();
                
                if(this._isShown) {
                    this.ui.voteBoxCntr.append(this._voteBoxView.$el);
                    this._voteBoxView.delegateEvents();
                }
            }
        },
                
        onShow: function() {
            if( this._voteBoxView )
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
            
            var candidateVote = Candidates.getLastVote(this.model.get('id'));
            
            if(candidateVote)
                model.set('vote', candidateVote);
            
            if(this.model.getStatusText() === 'Registered' && Candidates.getElection().checkStatus('Election'))
                this._voteBoxView = new Candidates.VoteBoxView({model: model });
            else
                this._voteBoxView = false;
        }
    });
    
    var CandItemView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#cand-list-item-tpl',
        
        modelEvents: {
            'change': 'render'
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
        
        appendHtml: function(collectionView, itemView, index){
            var childrenContainer = collectionView.itemViewContainer ? collectionView.$(collectionView.itemViewContainer) : collectionView.$el;
            var children = childrenContainer.children();
            if (children.size() <= index) {
              childrenContainer.append(itemView.el);
            } else {
              children.eq(index).before(itemView.el);
            }
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
    
    var Cands = FeedCollection.extend({
        limit: 30,
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
    
    var ApprovedCands = Cands.extend({       
        comparator: function(model) {
            return model.get('electoral_list_pos');
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
        
        isRevoked: function() {
            return (this.get('status') === 2);
        },
        
        canVote: function() {
            var passedVote = Candidates.votes.getPassedVote();
            var canVote = false;
            
            if((!passedVote && Candidates.getRevoteAbility().isAllowed('pass')) 
                || (_.isEqual(passedVote, this) && Candidates.getRevoteAbility().isAllowed('revoke'))
            ) {
                canVote = true;
            }
            
            return canVote;
        },
        
        revoke: function(options) {
            this.set('status', 2);
            this.save({}, options);
        },
        
        decline: function(options) {
            this.set('status', 1);
            this.save({}, options);
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
                        Candidates.votes.trigger('vote:passed', model);
                        success();
                    }, this)
                }
            );    
        },
                
        declineVote: function(success) {
            var vote = this.get('vote');
            
            if(!vote)
                return;
  
            vote.revoke({
                wait: true,
                success: success
            });
        },
                
        updateAttrs: function() {
    
            var vote = this.get('vote');
            
            if(vote) {
                var declined = vote.isDeclined();
                
                this.set({
                   voted: !vote.isRevoked(),
                   active:  (!declined && vote.canVote()),
                   declined: declined
                });                
            } else {
                var hasPassedVote = false;
                
                if(Candidates.votes.getPassedVote())
                    hasPassedVote = true;
                
                this.set({
                    voted: false,
                    active: (!hasPassedVote && Candidates.getRevoteAbility().isAllowed('pass')),
                    declined: false
                });
            }
        },        
             
        canVote: function() {
            return this.get('active');
        },
                
        initialize: function() {
            this.on('change:vote', _.bind(function(m, v, o) {
                this.updateAttrs();
            }, this));
            
            this.listenTo(Candidates.votes, 'sync refresh:availability', this.updateAttrs);
//            this.listenTo(Candidates.votes, 'refresh:availability', function() {
//                this.updateAttrs();
//            });
            
            this.updateAttrs();
        }
    });
    
    //Calculates and exposes information about revoting ability
    Candidates.RevoteAbility = Backbone.Model.extend({
        
        defaults: {
            removeVoteTime: null,
            revoteTime: null,
            revoteTriesRemain: null,
            revokeVoteTimeRemain: null,
            passVoteTimeRemain: null
        },
        
        _election: null,
        
        _votes: null,
        
        calculate: function() {
            var revoteTriesRemain = this.calculateRevoteTriesRemain();
            
            this.set('revoteTriesRemain', revoteTriesRemain);
            
            if(!revoteTriesRemain) {
                this.set('revokeVoteTimeRemain', 0);
                this.set('passVoteTimeRemain', 0);
                return;
            }
            
            this.set('revokeVoteTimeRemain', this.calculateRevokeVoteTimeRemain());
            this.set('passVoteTimeRemain', this.calculatePassVoteTimeRemain());
        },
        
        calculateRevoteTriesRemain: function() {
            var revokedVotes = [];
            var revoteTriesRemain = 0;
            
            this._votes.each(function(vote) {
                if(vote.isRevoked()) revokedVotes.push(vote);
            });
            
            revoteTriesRemain = this._election.get('revotes_count') - revokedVotes.length;
            
            var lastVote = this._getLastVote();
            
            if(lastVote && lastVote.isRevoked()) {
                revoteTriesRemain++;
            }
            
            if(revoteTriesRemain <= 0) 
                revoteTriesRemain = 0;
            
            return revoteTriesRemain;
        },
        
        calculateRevokeVoteTimeRemain: function() {
            var lastVote = this._getLastVote();
            var timeRemain = 0;
            var currentTime = this._getCurrentTime();
            
            if(!lastVote || lastVote.isRevoked() || lastVote.isDeclined())
                return 0;
            
            timeRemain = this._election.get('remove_vote_time') + lastVote.get('date') - currentTime;
            
            if(timeRemain <= 0)
                timeRemain = 0;
            
            return timeRemain;
        },
        
        calculatePassVoteTimeRemain: function() {
            var lastVote = this._getLastVote();
            var timeRemain = 0;
            var currentTime = this._getCurrentTime();
            
            if(!lastVote || !lastVote.isRevoked() || lastVote.isDeclined())
                return 0;
            
            timeRemain = this._election.get('revote_time') + lastVote.get('date') - currentTime;
            
            if(timeRemain <= 0)
                timeRemain = 0;
            
            return timeRemain;
        },
        
        isAllowed: function(action) {
            var lastVote = this._getLastVote();
            
            if(!lastVote && action === 'pass')
                return true;
            else if(!lastVote)
                return false;
            
            this.calculate();
            
            if(lastVote.isDeclined() && action === 'pass') {
                return true;
            } else if(this.get('revoteTriesRemain') > 0 && (
                (action === 'revoke' && this.get('revokeVoteTimeRemain') > 0)
                || (action === 'pass' && this.get('passVoteTimeRemain') > 0)
            )) return true;
            
            return false;
        },
        
        initialize: function(attrs, options) {
            this._election = options.election;
            this._votes = options.votes;
            
            this.set('removeVoteTime', this._election.get('remove_vote_time'));
            this.set('revoteTime', this._election.get('revote_time'));
            
            this.listenTo(this._votes, 'change:status add', this.calculate);
            
            this.calculate();
        },
        
        _getLastVote: function() {
            var lastVote = null;

            Candidates.votes.each(function(vote) {
                if(!lastVote) 
                    lastVote = vote;
                else if(lastVote.get('date') < vote.get('date'))
                    lastVote = vote;
            });  

            return lastVote;
        },
        
        _getCurrentTime: function() {
            return (new Date()).getTime();
        }
    });
    
    Candidates.Votes = FeedCollection.extend({
        
        limit: 50,
        
        model: Candidates.Vote,
        url: UrlManager.createUrlCallback('api/vote'),

        electionId: null,

        _acceptedVotesCount: 0,
                
        setAcceptedVotesCount: function(count) {
            var prevCount = this._acceptedVotesCount;

            if(count == prevCount) 
                return;

            this._acceptedVotesCount = count;
            this.trigger('changed:acceptedVotesCount', this._acceptedVotesCount, prevCount);
        },

        getAcceptedVotesCount: function() {
            return this._acceptedVotesCount;
        },

        setElectionId: function(value) {
            this.electionId = value;
            this.filter.election_id = value;
        },

        parse: function(response, options) {
    
            var result = FeedCollection.prototype.parse.apply(this, arguments);
    
            this.setAcceptedVotesCount(parseInt(response.data.acceptedCount) || 0);
    
            return result;
            
        }, 

        /**
        * Returns the vote that had been passed by current user and had not been declined by candidate 
        * @returns {Vote}
        */
        getPassedVote: function() {
            return this.find(function(m) {
                return ( m.isVoted() && ( !m.isDeclined() && !m.isRevoked() )); 
            });
        },
        
        initialize: function() {
            
            FeedCollection.prototype.initialize.apply(this, arguments);
            
            this.on('change:status', function(m, val, opts) {
                if(m.isDeclined() || m.isRevoked()) {
                    this.setAcceptedVotesCount(this._acceptedVotesCount - 1);
                }
            });
            
            this.on('vote:passed', function(vote) {
                this.setAcceptedVotesCount(this._acceptedVotesCount + 1);
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
    
    this.getElection = function() {
        return election;
    };
    
    this.getLastVote = function(candidateId) {
        var lastCandidateVote = null;

        Candidates.votes.each(function(vote) {
            if(vote.get('candidate_id') != candidateId) return;

            if(!lastCandidateVote) 
                lastCandidateVote = vote;
            else if(lastCandidateVote.get('id') < vote.get('id'))
                lastCandidateVote = vote;
        });  
        
        return lastCandidateVote;
    };
    
    this.getRevoteAbility = function() {
        if(!this._revoteAbility)
            this._revoteAbility = new Candidates.RevoteAbility({}, {
                votes: Candidates.votes,
                election: Candidates.getElection()
            });
        
        return this._revoteAbility;
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
        
        Candidates.Details.layout.votes.close();
        Candidates.Details.layout.controls.close();
        Candidates.Details.layout.info.close();
        
        this._detailsViewing = false;
    };
    
    this.addInitializer(function(options) {
        
        this.setOptions(options);
        
        this.cands = new Cands();
        
        this.approvedCands = new ApprovedCands();
        this.approvedCands.filter.status = 2;
        // current user's votes for all candidates
        this.votes = new Candidates.Votes();
        
        this.layout = new CandidatesLayout();
        
        election = new Election({
            id: config.electionId
        });
        
        var layoutShowDef = $.Deferred();
        
        this.layout.on('show', function() {
            layoutShowDef.resolve();
        });
        
        $.when(election.fetch(), layoutShowDef).then(_.bind(function() {            
            
            if(election.checkStatus('Election') || election.checkStatus('Finished')) {
                this.electoralList = new ElectoralList({
                    collection: this.approvedCands
                });

                this.layout.electoralList.show(Candidates.electoralList);
                $('#electoral-list-tab-sel').show();
                $('#electoral-list-tab-sel > a').tab('show');
            }

            if(election.checkStatus('Finished')) {
                this.mandates = new Backbone.Collection([], {
                    url: UrlManager.createUrl('api/mandate')
                });
                
                this.mandates.fetch({
                   params: {
                       filters: {
                           election_id: config.electionId
                       }
                   } 
                });          
            }
            
            this.candsList = new CandsListView({
                collection: this.cands
            });

            this.layout.candsList.show(Candidates.candsList);
            
        }, this));        
    });
    
    App.on('start', function() {
        
        Candidates.voteBoxModels = new Backbone.Collection();
        
        Candidates.cands.setElectionId(config.electionId);
        Candidates.votes.setElectionId(config.electionId);
        Candidates.votes.filter.with_profile = true;
        Candidates.votes.filter.user_id = WebUser.getId() || 0;
        
        Candidates.approvedCands.setElectionId(config.electionId);
        
        Candidates.votes.fetch()
          .done(function() {
              $.when(
                    Candidates.approvedCands.fetch(),
                    Candidates.cands.fetch()
              ).then(function(){

                Candidates.Details.start();

                if(Candidates.canInvite() && Candidates.getElection().checkStatus('Registration')) {
                    Candidates.Invite.start();
                    Candidates.layout.invite.show(Candidates.Invite.usersList);
                    Candidates.Invite.users.fetch();
                    $('#invite-tab-sel').show();
                }

                Backbone.history.start({
                    pushState: true,
                    root: UrlManager.createUrl('election/candidates/' + Candidates.getElectionId())
                });

              });
          });
    });
});


