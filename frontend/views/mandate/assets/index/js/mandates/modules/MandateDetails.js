/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('MandateDetails', function(MandateDetails, App, Backbone, Marionette, $, _) {
    
    // prevent starting with parent
    this.startWithParent = false; 
    
    var Elector = Backbone.Model.extend({
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
    
    var ElectorsCollection = FeedCollection.extend({
       limit: 30,
       model: Elector,
       url: UrlManager.createUrlCallback('api/vote'),
       
       getFilters: function() {
            return {
                accepted_only: true,
                with_profile: true
            };
       }
    });
    
    var ElectorView = Aes.ItemView.extend({
        className: 'user-info',
        template: '#electorfeed-item-tpl'
    });

    var DetailsLayout = Marionette.Layout.extend({
        template: '#mandate-details-layout-tpl',
                
        regions: {
            mandateInfo: '#mandate-info',
            tabs: '#mandate-tabs'
        }
    });

    this._mandateAcceptsPetitions = false;
    
    this.getActiveMandate = function() {
        return this._activeMandate;
    };    
    
    this.getMandates = function() {
        return this.options.getMandates();
    };
    
    this.checkMandateAcceptsPetitions = function(mandateId) {
        return $.ajax(UrlManager.createUrl('mandate/checkPetitionAcceptence/mandateId/' + mandateId), {
            success: function(response) {
                if(response.result === true)
                    MandateDetails._mandateAcceptsPetitions = true;
                else
                    MandateDetails._mandateAcceptsPetitions = false;
            },
            dataType: 'json'
        });
    };
    
    this.openCreatePetitionForm = function() {
        $('#createPetition-tab').html('Loading...');
        
        $('#createPetition-tab').load(
            UrlManager.createUrl("petition/ajaxCreate"),
            {
                mandateId: this.getActiveMandate().get('id'),
                ajax: true
            }
        );
    };
    
    this.onPetitionCreationFailed = function(response) {
        $('#createPetition-tab').html(response.responseHtml);
    };
    
    this.onPetitionCreated = function() {
        App.module('PetitionsList').petitions.offset = 0;
        App.module('PetitionsList').petitions.fetch();
        
        $('a[href="#petitions-tab"]').tab('show');
        $('#createPetition-tab').html('Loading...');
    };
    
    this.loadDetails = function(mandateId) {
        this.modPetitions.stop();
        
        var mandate = this.getMandates().findWhere({id: mandateId});
        
        this._activeMandate = mandate;
        
        this._mandateElectors = new ElectorsCollection();
        this._mandateElectors.setFilters({
           election_id: mandate.get('election_id'),
           candidate_id: mandate.get('candidate_id')
        });
        
        $.when(
            this._mandateElectors.fetch(),
            this.checkMandateAcceptsPetitions(mandate.get('id'))
        ).done(_.bind(function() {
            
            this.modPetitions.start({
                mandateId: mandate.get('id'),
                petitionsCanBeRated: this._mandateAcceptsPetitions
            });
            
            this.triggerMethod('detailsReady');
        },this));
        
    };
    
    this.initElectorsFeedView = function() {
        return new Aes.FeedView({
                itemView: ElectorView,
                collection: this._mandateElectors,

                filters: {

                    enabled: true,

                    attributes: {
                        class: 'search-form pull-right span4'
                    },

                    uiAttributes: {
                       inputs: {
                            class: 'span12'
                       }
                    },

                    fields: {
                            name: {
                                label: 'Name',
                                type: 'text'
                            },
                            birth_place: {
                                label: 'Birth Place'
                            },
                            ageFrom: {
                                label: 'Age From',
                                validator: {
                                    required: false,
                                    min: 1,
                                    max: 100
                                }
                            },
                            ageTo: {
                                label: 'Age To',
                                validator: {
                                    required: false,
                                    min: 1,
                                    max: 100,
                                    greaterThan: {
                                        attr: 'ageFrom',
                                        validOnEqual: true
                                    }
                                }
                            },
                            gender: {
                                label: 'Gender',
                                type: 'select',
                                options: [
                                    {label: 'Any', value: '', selected: true},
                                    {label: 'Male', value: '1'},
                                    {label: 'Female', value: '2'}
                                ]
                            }
                     }

                   }
            });  
    };
    
    this.initDetailsTabs = function() {
        
        var that = this;
        
        var tabsConfig = {
            electors: {
               title: 'Electors',
               content: this.initElectorsFeedView()
            },
            petitions: {
               title: 'Petitions',
               content: this.modPetitions.layout
            },
            createPetition: {
               title: 'Create new petition',
               content: 'Loading...',
               onBeforeSelect: function(tab) {
                   that.openCreatePetitionForm();
                   return true;
               }
            }
        };
        
        if(!this._mandateAcceptsPetitions)
            delete tabsConfig.createPetition;
        
        return new Aes.TabsView({
            tabs: tabsConfig
        });
    };
    
    this.viewDetails = function(mandateId) {
        this.loadDetails(mandateId);
    };
    
    this.viewPetitionDetails = function(mandateId, petitionId) {
        
        var initPetitionDetailsTab = function() {
            var petition = MandateDetails.modPetitions.petitions.findWhere({id: petitionId});

            if(!petition)
                return;

            MandateDetails.modPetitions.initPetitionDetails(petition, function(detailsView) {
                MandateDetails.detailsLayout.tabs.currentView.add({
                   tabId: 'petition-' + petitionId,
                   title: petition.get('title'),
                   content: detailsView,
                   closable: true
                }).select();
            });
        };
        
        if (!this._activeMandate || this._activeMandate.get('id') !== mandateId) {
        
            $.when(
                $.Deferred(function() {
                    var self = this;

                    MandateDetails.once('detailsReady', function() {
                        self.resolve();
                    });
                }),
                $.Deferred(function() {
                    var self = this;

                    MandateDetails.modPetitions.once('ready', function() {
                        self.resolve();
                    });
                })
            ).done(function() {
                initPetitionDetailsTab();
            });

            this.loadDetails(mandateId);
            
        } else {
            initPetitionDetailsTab();
        }
    };
    
    this.onDetailsReady = function() {
        
        var mandate = this._activeMandate;
        
        this.detailsLayout.mandateInfo.show(new this.options.mandateView({
            template: '#mandate-detailed-tpl',
            model: mandate
        }));
        
        $('#mandate-details ul.breadcrumbs').append('<li class="node-viewDetails"><a href="#">' + mandate.get('name') + ' - ' + mandate.get('candidate').profile.displayName + '</a></li>');
  
        this.detailsLayout.tabs.show(this.initDetailsTabs());
        
    };
    
    this.addInitializer(function(options) {
        this.detailsLayout = new DetailsLayout();
        this.options = options;
        this.modPetitions = App.module('PetitionsList');
    });
    
    this.on('start', function() {
        var that = this;
        $('body').on('petitionCreated', function(event, response) {
            that.onPetitionCreated(response);
        });
        $('body').on('petitionCreationFailed', function(event, response) { 
            that.onPetitionCreationFailed(response);
        });
    });    
});

