/**
 *   
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('UsersVotes', function(UsersVotes, App, Backbone, Marionette, $, _) {
    
    var config = {
        userId: null,
        layoutTpl: '#votes-layout'
    };
    
    var VotedCandidate = Backbone.Model.extend({
        parse: function() {
            var attrs = Backbone.Model.prototype.parse.apply(this, arguments);
            
            attrs.vote_id = parseInt(attrs.votes[0].id);
            attrs.vote_declined = parseInt(attrs.votes[0].status) !== 0;
            attrs.vote_date = parseInt(attrs.votes[0].date * 1000);
            
            return attrs;
        }
    });
    
    var Candidates = Backbone.Collection.extend({
        model: VotedCandidate
    });
    
    var UserVote = Election.extend({
        parse: function() {
            var attrs = Election.prototype.parse.apply(this, arguments);
            
            this.candidates = new Candidates(attrs.candidates, {parse: true});
            delete attrs.candidates;
            
            return attrs;
        }
    });
    
    var UsersVotesCollection = FeedCollection.extend({
        limit: 30,
        model: UserVote,
        url: UrlManager.createUrlCallback('api/election'),
        userId: null,
        
        setUserId: function(value) {
            this.userId = value;
            this.filter.voter_id = value;
        }        
    });
    
    var ElectionView = Aes.ItemView.extend({
        template: '#election-tpl',
                
        ui: {
            votes: '.votes-container'
        },        
                
        getStatusClass: function() {
            var status = this.model.getStatusText();
            
            return 'status-' + status.toLowerCase();
        },      
                
        serializeData: function() {
            return _.extend(Aes.ItemView.prototype.serializeData.apply(this, arguments), {
               textStatus: this.model.getStatusText() 
            });
        },  
                
        onRender: function() {
            this.$el.removeClass();
            this.$el.addClass(this.getStatusClass());
            
            if(!this.votesView) {
                this.votesView = new VotedCandidatesView({
                    collection: this.model.candidates
                });
            }
            
            this.ui.votes.append(this.votesView.render().$el);
            this.votesView.delegateEvents();
        },
                
        onShow: function() {
            this.votesView.trigger('show');
        }
    });
    
    var VotedCandidateView = Aes.ItemView.extend({
        
        template: '#voted-candidate-tpl',
        
        ui: {
            rates: '.rates-container',
            comments: '.comments-container'
        },        
        
//        onRender: function() {            
//            if(!this._rates)
//            {
//                this._rates = RatesWidget.create({
//                    targetId: this.model.get('id'),
//                    targetType: 'Candidate',
//                    targetEl: this.ui.rates
//                });
//            }
//            
//            this.ui.rates.prepend(this._rates.render().$el);
//            this._rates.delegateEvents();
//            this._rates.bindEventsToTarget($('.rates', this.$el));
//            
//            if(!this._comments)
//            {
//                this._comments = CommentsWidget.create({
//                    targetId: this.model.get('id'),
//                    targetType: 'Candidate'
//                });
//            }
//            
//            this.ui.comments.prepend(this._comments.render().$el);
//            this._comments.delegateEvents();
//        },
//          
//        onShow: function() {
//            this._rates.trigger('show');
//            this._comments.trigger('show');
//        }        
    });
    
    var VotedCandidatesView = Marionette.CollectionView.extend({
        itemView: VotedCandidateView
    });
    
    var ElectionsFeedView = Aes.FeedView.extend({

        itemView: ElectionView,
        
        getFiltersConfig: function() {
            return {
                type: 'inTopPanel',
                
                enabled: true,

                submitBtnText: 'Filter',
                
                uiAttributes: {
                    form: {
                        class: 'navbar-search form-inline span4'
                    }
                },

                fields: {
                    name: {
                        label: 'Type election title',
                        type: 'text',
                        
                        uiAttributes: {
                            input: {
                                class: 'span6'
                            }
                        }
                    }
                }  
            };
        }
    });
    
    var Layout = Marionette.Layout.extend({
       regions: {
           nominations: '#votes-feed-container'
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
        
        this.usersVotes = new UsersVotesCollection();
        this.usersVotes.setUserId(config.userId);
        
        this.electionsFeedView = new ElectionsFeedView({
            collection: this.usersVotes
        });
        
        this.layout = new Layout({
           template: config.layoutTpl
        });
        
    });
    
    this.on('start', function() {
        
        this.usersVotes.fetch().done(function(){
           UsersVotes.layout.nominations.show(UsersVotes.electionsFeedView); 
        });
        
    });
    
});