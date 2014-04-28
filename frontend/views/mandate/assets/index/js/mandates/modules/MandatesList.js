/**
 * Lists mandates, and it's details
 *   
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
App.module('MandatesList', function(MandatesList, App, Backbone, Marionette, $, _) {
    
    var config = {
        layoutTpl: '#mandates-list-layout'
    };
    
    var Mandate = Backbone.Model.extend({
        parse: function(attrs) {
            
            attrs.submiting_ts = parseInt(attrs.submiting_ts) * 1000;
            attrs.expiration_ts = parseInt(attrs.expiration_ts) * 1000;
            
            return attrs;
        },
                
        getStatusText: function() {
            return Mandate.getStatuses()[this.get('status')];
        },

        checkStatus: function(statusText) {
            var statuses = Mandate.getStatuses();

            if(_.indexOf(statuses, statusText) === -1)
                throw new Error('Status "' + statusText + '" does not exist');

            return (statuses[this.get('status')] === statusText);
        }    
    }, {
        getStatuses: function() {
            return ['Active', 'Expired', 'Revoked'];
        }
    });
    
    var MandatesCollection = FeedCollection.extend({
        limit: 30,
        model: Mandate,
        url: UrlManager.createUrlCallback('api/mandate')
    });
    
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
    
    var MandateView = Aes.ItemView.extend({
        template: '#mandate-tpl',
                
        getStatusClass: function() {
            var status = this.model.getStatusText();
            
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
                
        onRender: function() {
            this.$el.removeClass();
            this.$el.addClass(this.getStatusClass());
        }
    });
    
    var ElectorView = Aes.ItemView.extend({
        className: 'user-info',
        template: '#electorfeed-item-tpl'
    });
    
    var MandatesFeedView = Aes.FeedView.extend({
        template: '#mandates-feed-tpl',
        itemView: MandateView,
        
        getFiltersConfig: function() {
            return {
                
                enabled: true,

                submitBtnText: 'Filter',

                uiAttributes: {
                    form: {
                        class: 'span3 well'
                    },
                    inputs: {
                        class: 'span12'
                    }
                },

                fields: {
                    name: {
                        label: 'Mandate name',
                        type: 'text',
                        
                        filterOptions: {
                            extendedFormat: true
                        }
                    },
                    owner_name: {
                        label: 'Owner name',
                        type: 'text'
                    },
                    status: {
                        label: 'Status',
                        type: 'select',
                        options: [
                            {label: 'Any', value: '', selected: true},
                            {label: 'Active', value: 0},
                            {label: 'Expired', value: 1},
                            {label: 'Revoked', value: 2},
                        ],
                                
                        filterOptions: {
                            extendedFormat: true
                        }
                    }
                }  
            };
        }
    });
    
    var Layout = Marionette.Layout.extend({
       regions: {
           mandates: '#mandates-feed-container',
           mandateDetails: '#mandate-details'
       } 
    });
    
    var DetailsLayout = Marionette.Layout.extend({
        
        ui: {
            createPetitionBtn: '.petition-create-btn'
        },
        
        template: '#mandate-details-layout-tpl',
        regions: {
            mandateInfo: '#mandate-info',
            electorsTabContent: '#electors-tab',
            petitionsTabContent: '#petitions-tab'
        }
    });

    this._mandateAcceptsPetitions = false;
    
    this.checkMandateAcceptsPetitions = function(mandateId) {
        return $.ajax(UrlManager.createUrl('mandate/checkPetitionAcceptence/mandateId/' + mandateId), {
            success: function(response) {
                if(response.result === true)
                    MandatesList._mandateAcceptsPetitions = true;
                else
                    MandatesList._mandateAcceptsPetitions = false;
            },
            dataType: 'json'
        });
    };
    
    this.setOptions = function(options) {
        config = _.extend(config, _.pick(options, _.keys(config)));
    };
    
    this.getActiveMandate = function() {
        return this._activeMandate;
    }
    
    this.viewMandates = function() {
        this._activeMandate = null;
        this.layout.mandateDetails.close();
        this.layout.mandates.$el.show();
        $('#mandate-details li.node-viewDetails').remove();
    };
    
    
    this.openCreatePetitionForm = function() {
        $('#create-petition-tab').html('Loading...');
        
        $('#create-petition-tab').load(
            UrlManager.createUrl("petition/ajaxCreate"),
            {
                mandateId: this.getActiveMandate().get('id'),
                ajax: true
            }
        );
    };
    
    this.onPetitionCreationFailed = function(response) {
        console.log('Petition creation failed');
        $('#create-petition-tab').html(response.responseHtml);
    };
    
    this.onPetitionCreated = function() {
        console.log('Petition created');
        App.module('PetitionsList').petitions.offset = 0;
        App.module('PetitionsList').petitions.fetch();
        
        $('a[href="#petitions-tab"]').tab('show');
        $('#create-petition-tab').html('Loading...');
    };
    
    this.viewDetails = function(mandateId) {
        
        var mandate = this.mandates.findWhere({id: mandateId});
        
        this._activeMandate = mandate;
        
        var electors = new ElectorsCollection();
        electors.setFilters({
           election_id: mandate.get('election_id'),
           candidate_id: mandate.get('candidate_id')
        });

        this.layout.mandates.$el.hide();
        this.layout.mandateDetails.show(this.detailsLayout);
        
        this.detailsLayout.ui.createPetitionBtn.hide();
        
        this.detailsLayout.mandateInfo.show(new MandateView({
            template: '#mandate-detailed-tpl',
            model: mandate
        }));        
        
        $('#mandate-details ul.breadcrumbs').append('<li class="node-viewDetails"><a href="#">' + mandate.get('name') + ' - ' + mandate.get('candidate').profile.displayName + '</a></li>');        
        
        $.when(
            electors.fetch(),
            this.checkMandateAcceptsPetitions(mandate.get('id'))
        ).done(_.bind(function() {
            if (this._mandateAcceptsPetitions) {
                this.detailsLayout.ui.createPetitionBtn.show();
                $('a[href="#create-petition-tab"]').click(_.bind(this.openCreatePetitionForm, this));
            }
            
            this.modPetitions = App.module('PetitionsList');
            this.modPetitions.start({
                mandateId: mandate.get('id'),
                petitionsCanBeRated: this._mandateAcceptsPetitions
            });
            
            this.detailsLayout.electorsTabContent.show(new Aes.FeedView({
                itemView: ElectorView,
                collection: electors,

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
                                type: 'text',
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
            }));
            this.detailsLayout.petitionsTabContent.show(this.modPetitions.layout);            
        }, this));
    };
    
    this.addInitializer(function(options) {
        
        this.setOptions(options);
        
        this.mandates = new MandatesCollection();
        
        this.mandatesFeedView = new MandatesFeedView({
            collection: this.mandates
        });
        
        this.layout = new Layout({
           template: config.layoutTpl
        });
        
        this.detailsLayout = new DetailsLayout();
    });
    
    this.on('start', function() {
        
        this.mandates.fetch().done(function(){
           MandatesList.layout.mandates.show(MandatesList.mandatesFeedView);
           MandatesList.trigger('ready');
        });
        
        var that = this;
        $('body').on('petitionCreated', function(event, response) {
            that.onPetitionCreated(response);
        });
        $('body').on('petitionCreationFailed', function(event, response) { 
            that.onPetitionCreationFailed(response);
        });
    });
    
});