/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Election = Backbone.Model.extend({
    urlRoot: UrlManager.createUrlCallback('api/election'),

    getStatusText: function() {
        return Election.getStatuses()[this.get('status')];
    },

    checkStatus: function(statusText) {
        var statuses = Election.getStatuses();

        if(_.indexOf(statuses, statusText) === -1)
            throw new Error('Status "' + statusText + '" does not exist');

        return (statuses[this.get('status')] === statusText);
    },

    parse: function() {
        var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

        attrs.status = parseInt(attrs.status);
        attrs.id = parseInt(attrs.id);

        attrs.revotes_count = parseInt(attrs.revotes_count);
        //We are getting minutes from server here we convert it to the correct
        //time interval in milliseconds
        attrs.remove_vote_time = parseInt(attrs.remove_vote_time) * 60 * 1000;  
        attrs.revote_time = parseInt(attrs.revote_time) * 60 * 1000;

        return attrs;
    }
}, {
    getStatuses: function() {
        return ['Published', 'Registration', 'Election', 'Finished', 'Canceled'];
    }
});

