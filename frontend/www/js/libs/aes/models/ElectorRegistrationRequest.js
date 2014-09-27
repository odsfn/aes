var ElectorRegistrationRequest = Backbone.Model.extend({
    
    defaults: {
        user_id: null,
        election_id: null,
        data: null
    },
    
    urlRoot: UrlManager.createUrlCallback('api/electorRegistrationRequest'),
    parse: function() {
        var attrs = Backbone.Model.prototype.parse.apply(this, arguments);

        attrs.id = parseInt(attrs.id);
        attrs.user_id = parseInt(attrs.user_id);
        attrs.election_id = parseInt(attrs.election_id);

        if(attrs.profile)
        {
            var profile = new Aes.User(attrs.profile, {parse: true});
            _.extend(attrs, profile.attributes);
            
            delete attrs.profile;
        }
        
        return attrs;
    },

    toJSON: function(options) {
        var json = Backbone.Model.prototype.toJSON.call(this);

        if(json.profile)
            delete json.profile;
        
        if(options && options.success !== undefined)
            json = _.pick(json, 'id', 'user_id', 'election_id', 'data');

        return json;
    }
}, {
    STATUS_AWAITING_ADMIN_DECISION: 0,
    STATUS_AWAITING_USERS_DECISION: 1,
    STATUS_REGISTERED: 9,
    STATUS_DECLINED: 10
});

