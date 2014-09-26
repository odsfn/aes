Ext.define('ElectoralGroups.view.requestsgrid.RequestsGridController', {
    extend: 'Ext.app.ViewController',

    requires: [
        'Ext.MessageBox',
        'ElectoralGroups.model.ElectorRegistrationRequest'
    ],

    alias: 'controller.requestsgrid',

    updateElectorRegRequestStatus: function(view, record, status) {
        view.mask();
        ElectoralGroups.model.ElectorRegistrationRequest.load(
            record.get('electorRegistrationRequest').id,
            {
                success: function(request) {
                    request.set(
                        'status', 
                        status
                    );
                    request.save({
                        callback: function(records, operation, success) {
                            if (success) {
                                view.getStore().reload();
                                view.unmask();
                            } else {
                                throw new Error('Operation failed');
                            }
                        }
                    });
                }
            }
        );
    },

    onAcceptClick: function (view, rowIndex, colIndex, item, e, record, row) 
    {
        this.updateElectorRegRequestStatus(
            view, record,
            ElectoralGroups.model.ElectorRegistrationRequest.STATUS_REGISTERED
        );
    },

    onDeclineClick: function(view, rowIndex, colIndex, item, e, record, row) 
    {
        this.updateElectorRegRequestStatus(
            view, record,
            ElectoralGroups.model.ElectorRegistrationRequest.STATUS_DECLINED
        );
    }
    
//    init: function() {
//        var grid = this.view;
//        
//        this.view.getSelectionModel().on('selectionchange', function(selModel, selections){
//            grid.down('#declineButton').setDisabled(selections.length === 0);
//            grid.down('#confirmButton').setDisabled(selections.length === 0);
//        });
//    }
});

