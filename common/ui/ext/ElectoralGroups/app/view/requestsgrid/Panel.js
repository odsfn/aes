/* 
 * This Panel is contrainer for requestsgrid with requestdetails
 */
Ext.define('ElectoralGroups.view.requestsgrid.Panel', {
    requires: [
        'ElectoralGroups.view.requestsgrid.RequestsGrid',
        'ElectoralGroups.view.requestsgrid.PanelController',
        'ElectoralGroups.view.requestsgrid.PanelDetail'
    ],
    extend: 'Ext.panel.Panel',
    xtype: 'requestspanel',
    controller: 'requestspanel',
    layout: {
        type: 'border'
    },
    items: [
        {
            xtype: 'requestsgrid',
            region: 'center',
            layout: 'fit',
            flex: 5
        },
        {
            xtype: 'requestdetail',
            region: 'east',
            layout: 'fit',
            flex: 2,
            split: true
        }
    ]
});

