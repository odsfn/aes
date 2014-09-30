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

    updateElectorRegRequestsStatuses: function(items, status, successHandler, failureHandler, allDoneHandler) {
        var savingCount = 0,
            cycleFinished = false;
        
        Ext.each(items, function(item, index) {
            
            //is the last item
            if(items.length == index + 1)
                cycleFinished = true;
            
            ElectoralGroups.model.ElectorRegistrationRequest.load(
                item.get('electorRegistrationRequest').id,
                {
                    success: function(request) {
                        request.set(
                            'status', 
                            status
                        );
                        savingCount++;
                        request.save({
                            callback: function(records, operation, success) {
                                if (success) {
                                    if (successHandler && typeof(successHandler) === 'function')
                                        successHandler(records, operation);
                                } else {
                                    if (failureHandler && typeof(failureHandler) === 'function')
                                        failureHandler(records, operation);
                                }
                                
                                if (cycleFinished && --savingCount === 0 
                                    && allDoneHandler && typeof(allDoneHandler) === 'function')
                                    allDoneHandler();
                            }
                        });
                    }
                }
            );
        });
    },

    updateStatusOnManyRows: function(status) {
        this.view.mask('Saving...');
        var items = this.view.getSelectionModel().getSelection();
        this.updateElectorRegRequestsStatuses(
            items,
            status,
            false,
            false,
            Ext.bind(function() {
                this.view.unmask();
                this.view.getStore().reload();
            }, this)
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
    },
    
    onClickAcceptMany: function() {
        this.updateStatusOnManyRows(ElectoralGroups.model.ElectorRegistrationRequest.STATUS_REGISTERED);
    },
    
    onClickDeclineMany: function() {
        this.updateStatusOnManyRows(ElectoralGroups.model.ElectorRegistrationRequest.STATUS_DECLINED);
    },

    init: function() {
        var grid = this.view;
        
        this.view.getSelectionModel().on('selectionchange', function(selModel, selections){
            grid.down('#confirmManyBtn').setDisabled(selections.length === 0);
            grid.down('#declineManyBtn').setDisabled(selections.length === 0);
        });
    }
});

