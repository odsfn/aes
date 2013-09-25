/* 
 * This class provides internationalization functionality to the ClientApps relied 
 * on Backbone Marionette
 * 
 * @TODO: 
 * 1. Implement Local setting
 * 2. Implement messages translations
 * 3. Implement parametrized messages
 */
var i18n = function() {

    var
    /**
     * Locale settings with defaults
     * @type {Map}
     */
    local = {
        messages: [],

        dateFormat: {
            short: 'dd/MM/yyyy',
            medium: '',
            full: ''
        },
                
        timeFormat: {
            short: 'HH:mm',
            medium: 'HH:mm:ss',
            full: 'hh:mm:ss a'
        }
    },
    /**
     * Translate msg to current language
     * @param {String} msg
     * @param {hash} params
     * @returns {String}
     */    
    translate = function(msg, params) {
        params = params || {};
        return _.template(msg, params, {
          interpolate: /\{(.+?)\}/g
        });
    },
    /**
     * Returns date in format corresponding with current Local
     * @param {Date} date   Current by default
     * @param {type} type   short|medium|full format ( For the current moment does not affects on returned value )
     * @returns {string}
     */        
    date = function(date, dateType, timeType) {
        date = date || new Date();
        dateType = dateType || 'short';
        
        var format = local.dateFormat[dateType];
        
        if(timeType)
            format += local.timeFormat[timeType];
        
        return $.format.date(date.getTime(), format);
    },
    /**
     * This methods will be available in every template which is rendering by
     * Marionette.Renderer
     * @type hash
     */
    templateHelpers = {
        t: translate
    },
    
    /**
     * Sets local settings i.e. messages translations, date/time formats,
     * values formats.
     * 
     * This method should be called before client application starts, and conf
     * should contain local settings and translates, which are exported from 
     * Yii's PhpMessageSource
     * 
     * @param {Map} conf
     * @returns {Boolean}
     */        
    setLocal = function(conf) {};
    
    //Overriding Marionette Renderer
    var parentRender = Backbone.Marionette.Renderer.render;

    Backbone.Marionette.Renderer.render = function(template, data) {
      data = _.extend(data, templateHelpers);

      return parentRender(template, data);
    };
    
    return _.extend({
        "date": date,
        "setLocal": setLocal
    }, templateHelpers);
}();

