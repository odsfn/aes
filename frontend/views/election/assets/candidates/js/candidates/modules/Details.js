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
            mandates: '#mandates-tab'
        }
    });
    
    var DetailsView = Candidates.ElectoralCandView.extend({
       template: '#candidate-detailed' 
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
    
    this.viewCandidate = function(candidate) {
        currentCandidate = candidate;
        
        this.detailesView = new DetailsView({
            model: candidate
        });
        
        this.layout.info.show(this.detailesView);
        
        this.votes.reset();
        this.votes.filter.with_profile = true;
        this.votes.filter.candidate_id = candidate.get('id');
        this.votes.setElectionId(candidate.get('election_id'));
        
        this.layout.votes.show(this.votesFeedView);
        
        this.votes.fetch();
    };
    
    this.showLayout = function() {
        $('#candidate-details').html('');
        $('#candidate-details').append(this.layout.render().$el);
    };
    
    this.addInitializer(function(options) {
        this.layout = new MainLayout();
        this.votes = new Candidates.Votes();
        this.votesFeedView = new VotesFeedView({
            collection: this.votes
        });
    });
    
    this.on('start', function(options) {
        this.showLayout();
    });
    
});
