<div class="row-fluid">
    <?php if ($this->election->voter_group_restriction == Election::VGR_GROUPS_ADD): ?>
    <div class="assigned-groups-sel" style="display: none;">
        <p>If you want to register as elector you should specify one or more of these groups where you will be included.</p>
        <?php $groups = $this->election->localVoterGroups;
            $checkData = array();
            foreach ($groups as $group) {
                $checkData[$group->id] = $group->name;
            }

            echo TbHtml::checkBoxList('voter-groups', false, $checkData);
        ?>
    </div>
    <?php endif; ?>
    <button id="register-elector" class="btn btn-large span8 offset2">Register as Elector</button>
</div>
<script type="text/javascript">
    $(function() {
        var parent = $('#register-elector').parent();
        var onSuccess = function(model, response) {
                $('#register-elector').remove();

                if(response.status && response.status == 'exists_elector') {
                    Aes.Notifications.add(response.message, 'warning');
                } else {
                    if(response.status && response.status == 'exists') {
                        Aes.Notifications.add(response.message, 'warning');
                    } else if(model.get('status') == ElectorRegistrationRequest.STATUS_REGISTERED) {
                        Aes.Notifications.add('You have been registered as elector.', 'success');
                    } else if(model.get('status') == ElectorRegistrationRequest.STATUS_AWAITING_ADMIN_DECISION) {
                        Aes.Notifications.add('Your registration request was sent. Election manager will consider it as soon as possible.', 'success');
                    }               

                    $('body').trigger('elector_registered', [model]);
                }
                
                parent.unmask();
            },
            registerClickHandler = function(){

                parent.mask();

                var regReq = new ElectorRegistrationRequest({
                    user_id: <?= Yii::app()->user->id ?>,
                    election_id: <?= $this->election->id ?>
                });

                regReq.save({}, {
                    success: function(model, response) {
                        onSuccess(model, response);
                    }
                });
            },
            registerClickHandlerWithGroups = function(){
                var modal = new (Aes.ModalView.extend({
                    label: 'Elector Registration',
                    body: $('.assigned-groups-sel').html(),

                    triggers: {
                        'click button.close': 'closeClicked',
                        'change input[type="checkbox"]': 'selectionChanged'
                    },

                    onSelectionChanged: function() {
                        var enabled = this.$el
                            .find('input[type="checkbox"]:checked')
                            .length > 0;

                        var regBtn = this.$el.find('.modal-footer > .btn');

                        if (enabled) {
                            regBtn.attr('disabled', false);
                        } else
                            regBtn.attr('disabled', 'disabled');
                    },                      

                    buttonsConfig: function() {
                        return [
                            new Aes.ButtonView({
                                label: 'Register',

                                attributes: {
                                    class: 'btn',
                                    disabled: 'disabled'
                                },

                                onClick: _.bind(function() {
                                    var me = this,
                                        groups = [];

                                    this.$el.parent('.modal').mask();

                                    _.each(
                                        $('.modal-body input[type="checkbox"]:checked'),
                                        function(checkbox, index) {
                                            groups.push($(checkbox).val());
                                        }
                                    );

                                    var regReq = new ElectorRegistrationRequest({
                                        user_id: <?= Yii::app()->user->id ?>,
                                        election_id: <?= $this->election->id ?>,
                                        data: {
                                            groups: groups
                                        }
                                    });

                                    regReq.save({}, {
                                        success: function(model, response) {
                                            onSuccess(model, response);
                                            me.triggerMethod('closeClicked');
                                        }
                                    });
                                }, this)
                            })         
                        ];
                    }
                }));

                modal.open();
            };

        <?php if($this->election->voter_group_restriction == Election::VGR_GROUPS_ADD): ?>
            $('#register-elector').click(registerClickHandlerWithGroups);
        <?php else: ?>
            $('#register-elector').click(registerClickHandler);
        <?php endif; ?>
    });
</script>

