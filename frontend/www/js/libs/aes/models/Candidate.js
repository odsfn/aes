/* 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
var Candidate = Backbone.Model.extend({

    urlRoot: UrlManager.createUrlCallback('api/candidate'),

    parse: function() {
        var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

        attrs.id = parseInt(attrs.id);
        attrs.user_id = parseInt(attrs.user_id);
        attrs.election_id = parseInt(attrs.election_id);
        attrs.electoral_list_pos = parseInt(attrs.electoral_list_pos);
        attrs.status_changed_ts = parseInt(attrs.status_changed_ts) * 1000;
        attrs.profile.birth_day = parseInt(attrs.profile.birth_day) * 1000;
        attrs.profile.user_id = parseInt(attrs.profile.user_id);

        return attrs;
    },

    toJSON: function(options) {
        var json = Backbone.Model.prototype.toJSON.call(this);

        return json;
    },

    getStatusText: function() {
        return Candidate.getStatuses()[this.get('status')];
    },

    setStatus: function(statusText) {
        this.set('status', this.getStatusId(statusText));
    },

    checkStatus: function(statusText) {

        this.getStatusId(statusText);

        var statuses = Candidate.getStatuses();

        return (statuses[this.get('status')] === statusText);
    },

    getStatusId: function(statusText) {
        var id = false;

        var statuses = Candidate.getStatuses();

        id = _.indexOf(statuses, statusText);

        if(id === -1)
            throw new Error('Status "' + statusText + '" does not exist');            

        return id;
    }

}, {
    getStatuses: function() {
        return ['Invited', 'Awaiting registration confirmation', 'Registered', 'Refused', 'Blocked'];
    }
});

