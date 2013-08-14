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
     * Translate msg to current language
     * @param {String} msg
     * @param {hash} params
     * @returns {String}
     */    
    translate = function(msg, params) {
        return msg;
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
        "setLocal": setLocal
    }, templateHelpers);
}();

