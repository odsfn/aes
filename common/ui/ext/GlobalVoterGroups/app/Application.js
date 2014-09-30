/**
 * The main application class. An instance of this class is created by app.js when it calls
 * Ext.application(). This is the ideal place to handle application launch and initialization
 * details.
 */
Ext.define('GlobalVoterGroups.Application', {
    requires: [
        'Aes.store.VoterGroups'
    ],
    
    extend: 'Ext.app.Application',
    
    name: 'Voter Groups',

    stores: [
        'Aes.store.VoterGroups'
    ],
    
    init: function(app) {
        window.appConfig = window.appConfig || {}; 
        app.options = Ext.clone(window.appConfig);
    },
    
    launch: function () {
        this.onLaunchExternal();
    },
    
    onLaunchExternal: function() {
        var onLaunch = window.appConfig.onLaunch || null;
        if (onLaunch && typeof(onLaunch) === 'function')
            onLaunch();
    },
    
    options: {
        userId: null
    }
});
