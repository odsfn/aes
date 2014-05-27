var PetitionView = Aes.ItemView.extend({
        /*
         * Whether to shorten content
         */
        shortContent: true,
        
        personType: 'creator',
        
        template: '#petition-tpl',      
        
        ui: {
            rates: '.petition-rates'
        },
        
        onRender: function() {
            this.ui.rates.prepend(this._rates.render().$el);
            this._rates.delegateEvents();
        },
                
        onShow: function() {
            this._rates.trigger('show');
        },
                
        serializeData: function() {
            
            var person = this.model.get('mandate').candidate.profile;
            var personType = Marionette.getOption(this, 'personType');
            
            if(personType === 'creator') {
                person = this.model.get('creator');
            }
    
            var shortContent = false;
            
            if (Marionette.getOption(this, 'shortContent')) {
                shortContent = this.getShortContent();
            }
    
            return _.extend(Aes.ItemView.prototype.serializeData.apply(this, arguments), {
               shortContent: shortContent,
               personType: personType,
               person: person
            });
        },
                
        getShortContent: function() {
            var text = this.model.get('content');
            var length = 512;
            
            if(text.length > length) {
                text = text.substr(0, length) + '...';
            }
            
            return text;
        },
        
        getRates: function() {
            return this._rates;
        },
        
        canRateChecker: function() {
            return false;
        },
        
        _initializeRates: function() {
            this._rates = RatesWidget.create({    
                rateViewTemplate: '#petition-rates-tpl',

                targetId: this.model.get('id'),
                targetType: 'Petition',

                canRateChecker: Marionette.getOption(this, 'canRateChecker')
            });
        },
        
        initialize: function() {
            Aes.ItemView.prototype.initialize.apply(this, arguments);
            
            this._initializeRates();
        }
    });