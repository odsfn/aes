/**
 * This class is the main view for the application. It is specified in app.js as the
 * "autoCreateViewport" property. That setting automatically applies the "viewport"
 * plugin to promote that instance of this class to the body element.
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */
Ext.define('ElectoralGroups.view.main.Main', {
    
    extend: 'Ext.panel.Panel',

    xtype: 'app-main',
    
    controller: 'main',
    
    viewModel: {
        type: 'main'
    },
    
    layout: {
        type: 'border'
    },

    items: [{
        xtype: 'groupsgrid',
        bind: {
            title: '{name}'
        },
        region: 'west',
        flex: 2,
        split: true
    },{
        region: 'center',
        xtype: 'tabpanel',
        title: 'Electors',
        flex: 4,
        items:[{
            title: 'Registered in Election',
            closable: false,
            xtype: 'electorsgrid',
            store: {
                fields : [ 'first_name', 'last_name', 'email', 'birth_day', 'birth_place', 'gender' ],
                data   : [
                    [ 'Bill', 'Geits', 'billy@msdn.com', 978300000, 'Aiowa, USA', 1 ],
                    [ 'Edgar', 'Po', 'po@writers.com', 778300000, 'Somewhere', 1 ],
                    [ 'Suzy', 'Cherlies', 'suzy@test.com', 725300120, 'Somewhere else, USA', 2 ],
                    [ 'It', 'King', 'it@horror.org', 999300120, 'Somewhere else, USA', 0 ]
                ]
            }
        }]
    }]
});
