var Petition = Backbone.Model.extend({

    parse: function(attrs) {

        attrs.created_ts = parseInt(attrs.created_ts) * 1000;

        return attrs;
    }
});
