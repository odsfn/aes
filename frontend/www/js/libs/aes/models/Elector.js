var Elector = Backbone.Model.extend({
    
    defaults: {
        user_id: null,
        election_id: null
    },
    
    urlRoot: UrlManager.createUrlCallback('api/elector'),
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

        return json;
    }
});

