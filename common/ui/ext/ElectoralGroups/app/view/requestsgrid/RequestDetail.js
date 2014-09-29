/* 
 * This Panel displays details of elector registration request with information
 * of selected groups if any.
 * 
 * It supports manager by ability to change groups where user will be added
 */
Ext.define('ElectoralGroups.view.requestsgrid.PanelDetail', {
    requires: [
        'Ext.form.CheckboxGroup'
    ],
    extend: 'Ext.panel.Panel',
    xtype: 'requestdetail',
    html: 'Details will be here',
    title: 'Request details',
    layout: {
        type: 'vbox',
        align: 'stretch'
    },
    items: [
        {
            xtype: 'panel',
            defaults: {
                padding: 10
            },
            items: [
                {
                    xtype: 'checkboxgroup',
                    fieldLabel: '<b>Check groups</b> where user will be added after registration',
                    layout: 'vbox'
                },
                {
                    itemId: 'groups',
                    xtype: 'box',
                    html: '<b>Groups specified by user:</b> <span class="values">None</span>'
                }
            ]
        }
    ],
    fbar: [
        {
            itemId: 'btnDecline',
            xtype: 'button',
            text: 'Decline'
        },
        {
            itemId: 'btnAccept',
            xtype: 'button',
            text: 'Accept'
        }
    ]
});

