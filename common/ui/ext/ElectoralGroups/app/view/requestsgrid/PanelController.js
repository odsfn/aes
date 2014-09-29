Ext.define('ElectoralGroups.view.requestsgrid.PanelController', function(PanelController) {
    var selectedRequest;
    
    var updateGroupsInRecord = function() {
            var groups = [],
                data = selectedRequest.get('data');

            var values = this.getView().down('requestdetail').down('checkboxgroup').getValue();
            
            if (!values.groups) {
                data.groups = [];
            } else if (typeof(values.groups) != 'object') {
                data.groups = [values.groups];
            } else {
                data.groups = values.groups;
            }

            selectedRequest.set('data', data);
        },
        updateRequestStatus = function(status) {
            this.getView().mask('Saving...');
            updateGroupsInRecord.call(this);
            selectedRequest.set('status', status);
            selectedRequest.save({
                callback: function(records, operation, success) {
                    if (success) {
                        var store = this.getView().down('requestsgrid').getStore();
                        this.getView().unmask();
                        this.openNextRequest();
                        store.remove(store.findRecord('user_id', selectedRequest.get('user_id')));
                    } else {
                        throw new Error('Operation failed');
                    }
                },
                scope: this
            });
        };
    
    return {
        extend: 'Ext.app.ViewController',

        alias: 'controller.requestspanel',

        control: {
            'requestsgrid': {
                selectionchange: function(selModel, selected, eOpts) {
                    this.lockDetailsPanel();
                    if(selected.length == 1) {
                        this.viewDetails(selected[0]);
                    } else {
                        selectedRequest = null;
                    }
                }
            },
            'requestdetail #btnDecline': {
                click: function() {
                    updateRequestStatus.call(this, ElectoralGroups.model.ElectorRegistrationRequest.STATUS_DECLINED);
                }
            },
            'requestdetail #btnAccept': {
                click: function() {
                    updateRequestStatus.call(this, ElectoralGroups.model.ElectorRegistrationRequest.STATUS_REGISTERED);
                }
            }
        },

        lockDetailsPanel: function() {
            var requestDetailView = this.getView().down('requestdetail');
            requestDetailView.down('checkboxgroup').removeAll();
            var el = requestDetailView.down('#groups').getEl();
            if (el)
                el.down('.values').setHtml('None');

            requestDetailView.disable();
        },

        viewDetails: function(model) {
            var requestDetailView = this.getView().down('requestdetail'),
                checkboxes = requestDetailView.down('checkboxgroup'),
                selectedGroupsValueEl = requestDetailView.down('#groups').getEl().down('.values');

            requestDetailView.mask('Loading...');
            ElectoralGroups.model.ElectorRegistrationRequest.load(
                model.get('electorRegistrationRequest').id,
                {
                    success: function(request) {
                        selectedRequest = request;
                        
                        var groupIds = request.getGroups(),
                            groupNames = [],
                            groupsStr = '';

                        ElectoralGroups.app.getStore('ElectionVoterGroups')
                            .each(function(groupAssignment) {
                                var group = ElectoralGroups.app.getStore('VoterGroups').getById(groupAssignment.get('voter_group_id'));

                                if(!group || group.get('type') == ElectoralGroups.model.VoterGroup.TYPE_GLOBAL)
                                    return;

                                var groupName = group.get('name'),
                                    checked = false;

                                if(groupIds.indexOf(group.get('id')) !== -1) {
                                    checked = true;
                                    groupNames.push(groupName);
                                }

                                checkboxes.add({
                                    type: 'checkboxfield',
                                    boxLabel  : groupName,
                                    name      : 'groups',
                                    checked: checked,
                                    inputValue: group.get('id')
                                });
                        }, this);

                        if(groupIds.length == 0)
                            groupsStr = 'None';
                        else
                            groupsStr = groupNames.join();                    

                        selectedGroupsValueEl.setHtml(groupsStr);

                        requestDetailView.unmask();
                        requestDetailView.enable();
                    }
                }
            );
        },

        openNextRequest: function() {
            this.lockDetailsPanel();
            
            var selModel = this.getView().down('requestsgrid').getSelectionModel();
            selModel.selectNext(false);
        },

        init: function() {
            this.lockDetailsPanel();
        }
}});

