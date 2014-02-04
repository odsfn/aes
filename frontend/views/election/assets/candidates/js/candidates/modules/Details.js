/**
 * Candidate details 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('Candidates.Details', function(Details, App, Backbone, Marionette, $, _) {
    
    // prevent starting with parent
    this.startWithParent = false; 
    
    var Candidates = App.module('Candidates');
    
    var currentCandidate;
    
    var MainLayout = Marionette.Layout.extend({
        template: '#candidate-details-layout',
        regions: {
            info: '#candidate-info',
            docs: '#docs-tab',
            votes: '#votes-tab',
            mandates: '#mandates-tab',
            controls: '#controls'
        }
    });
    
    var DetailsView = Candidates.ElectoralCandView.extend({
       template: '#candidate-detailed',
       
       serializeData: function() {
           return _.extend(Candidates.ElectoralCandView.prototype.serializeData.apply(this, arguments),
               {
                   electionStatusText: Candidates.getElection().getStatusText(),
                   votesCount: Candidates.Details.votes ? Candidates.Details.votes.getAcceptedVotesCount() : 0
               }
           );
       }
    });
    
    var ControlsView = Marionette.View.extend({
        
        modelEvents: {
            'change': 'render'
        },
        
        render: function() {
            
           var createChangeStatusCallback = function(cand, status) {
               
               var done = function() {
                   $('#candidate-details').css('cursor', 'default').unmask();
               };
               
               return function() {
                   $('#candidate-details').css('cursor', 'wait').mask();
                   Details.setCandStatus(cand, status, done);
               };
           }; 
            
           Candidates.ElectoralCandView.prototype.onRender.apply(this, arguments);
           
           var btn = '', election = Candidates.getElection();
           
           if( this.model.get('id') === Candidates.getCurrentCandidateId() && election.checkStatus('Registration') ) {
               
               if(this.model.checkStatus('Invited')) 
               {
                   btn = $('<button class="btn self-confirm">Confirm participation</button>');
                   btn.click(createChangeStatusCallback(this.model, 'Registered')); 
               } 
               else if(this.model.checkStatus('Registered')) 
               {
                   btn = $('<button class="btn self-refuse">Refuse from participation</button>');
                   btn.click(createChangeStatusCallback(this.model, 'Refused'));
               }
               
           } else if( Candidates.canInvite() ) {
               
               if(election.checkStatus('Registration') && !this.model.checkStatus('Registered') && !this.model.checkStatus('Refused') ) 
               {    
                   btn = $('<button class="btn refuse-from-reg">Refuse from registration</button>');
                   btn.click(createChangeStatusCallback(this.model, 'Refused'));   
               }
               else if (this.model.checkStatus('Registered') && (election.checkStatus('Election') || election.checkStatus('Registration')) ) 
               {
                   btn = $('<button class="btn block">Block this candidate</button>');
                   btn.click(createChangeStatusCallback(this.model, 'Blocked'));    
               }
               else if(election.checkStatus('Registration') && this.model.checkStatus('Refused'))
               {
                   btn = $('<button class="btn invite">Invite</button>');
                   btn.click(createChangeStatusCallback(this.model, 'Invited'));
               }
           }
               
           this.$el.html(btn);
           
           return this;
       }
       
    });
    
    var VoteView = Marionette.ItemView.extend({
        className: 'user-info',
        template: '#votefeed-item-tpl',
        
        modelEvents: {
            'change:status': 'render'
        },
        
        ui: {
            controls: 'span.controls',
            depriveBtn: 'span.controls > small'
        },
                
        triggers: {
            'mouseenter': 'mouseEnter',
            'mouseleave': 'mouseLeave',
            'click span.controls > small': 'declineBtnClick'
        },
                
        onMouseEnter: function() {
            var candidateId = Candidates.getCurrentCandidateId();
            
            if(!candidateId || this.model.get('candidate_id') != candidateId)
                return;
            
            this.ui.controls.show();
        },
                
        onMouseLeave: function() {
            this.ui.controls.hide();
        },
                
        onDeclineBtnClick: function() {
            this.model.set('status', 1);
            
            this.$el.mask();
            this.model.save({}, {
                wait: true,
                success: _.bind(function() {
                    this.$el.unmask();
                }, this)
            });
        }
    });
    
    var VotesFeedView = Candidates.FeedView.extend({
       itemView: VoteView
    });
    
    this.setCandStatus = function(cand, status, success) {
        var success = success || function() {};
        
        cand.setStatus(status);
        cand.save({}, {
            success: success
        });
    };
    
    this.viewCandidate = function(candidate) {
        currentCandidate = candidate;
        
        this.detailesView = new DetailsView({
            model: candidate
        });
        
        this.controlsView = new ControlsView({
           model: candidate 
        });        
        
        this.layout.info.show(this.detailesView);
        this.layout.controls.show(this.controlsView);
        
        if(Candidates.getElection().checkStatus('Election') && candidate.checkStatus('Registered'))
        {
            this.votesFeedView = new VotesFeedView({
                collection: this.votes
            });            
            
            this.votes.reset();
            this.votes.filter.with_profile = true;
            this.votes.filter.candidate_id = candidate.get('id');
            this.votes.setElectionId(candidate.get('election_id'));

            this.layout.votes.show(this.votesFeedView);

            this.votes.fetch();
            $('#details-votes-tab-sel').show();
            $('#details-votes-tab-sel a').tab('show');
            
            this.listenTo(this.votes, 'changed:acceptedVotesCount', this.detailesView.render);
        } else
            $('#details-votes-tab-sel').hide();
    };
    
    this.showLayout = function() {
        $('#candidate-details').html('');
        $('#candidate-details').append(this.layout.render().$el);
    };
    
    this.addInitializer(function(options) {
        this.layout = new MainLayout();
        this.votes = new Candidates.Votes();
    });
    
    this.on('start', function(options) {
        this.showLayout();
    });
    
});
