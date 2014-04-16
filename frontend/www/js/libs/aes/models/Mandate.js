/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
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