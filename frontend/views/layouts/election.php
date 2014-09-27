<?php

$this->breadcrumbs->add('Elections', '/election');

Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/layouts/election.css');

$this->clips['titleIcon'] = '<i class="icon-briefcase"></i>';

$this->beginClip('titleAfterBredcrumbsContent');?>

<h3 class="pull-right"><small><?php echo $this->election->text_status; ?></small></h3>

<?php 
$this->endClip();

$this->beginClip('mainPicture');

if($this->election->have_pic) {
    $this->widget('common.widgets.imageWrapper.ImageWrapper', array(
        'imageSrc' => $this->election->picUrl,                                
        'width' => '20%'
    ));
}

$this->endClip(); 


$this->beginClip('navigation');

$this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'pills',
    'stacked' => 'true',
    'items' => array(
        array(
            'label'=> Yii::t('election', 'Election page'), 
            'url'=> array('/election/view', 'id'=>$this->election->id)
        ),
        array(
            'label'=> Yii::t('election', 'Provisions'), 
            'url'=> array('/election/provisions', 'id'=>$this->election->id)
        ),
        array(
            'label'=> Yii::t('election', 'Candidates'), 
            'url'=> array('/election/candidates', 'id'=>$this->election->id)
        ),
        array(
            'label'=> Yii::t('election', 'Electorate'), 
            'url'=> array('/election/electorate', 'id'=>$this->election->id)
        ),
        array(
            'label'=> Yii::t('election', 'Admins'), 
            'url'=> array('/election/admins', 'id'=>$this->election->id)
        ),
        array(
            'label'=> Yii::t('election', 'Management'), 
            'url'=> array('/election/management', 'id'=>$this->election->id), 
            'visible' => 
                ( $canAdmin = Yii::app()->user->checkAccess(
                        'election_administration', 
                        array('election' => $this->election)
                ))
        ),
        array(
            'label'=> Yii::t('election', 'Voters and Groups'), 
            'url'=> array(
                '/election/manageVotersGroups', 'id'=>$this->election->id
            ), 
            'visible' => ( 
                $canAdmin && in_array($this->election->status, 
                    array(Election::STATUS_REGISTRATION, Election::STATUS_ELECTION)
                )
            )
        )
    )
));

?>

<div class="row-fluid">
    <div class="span12 actions">
    <?php
    if (Yii::app()->user->checkAccess(
            'election_askToBecameElector', 
            array(
                'election' => $this->election
            )
    )) {

        $cs = Yii::app()->clientScript;
        $cs->registerPackage('aes-common')
                ->registerPackage('loadmask')
                ->registerScriptFile('/js/libs/aes/models/User.js')
                ->registerScriptFile('/js/libs/aes/models/ElectorRegistrationRequest.js')
                ->registerScriptFile('/js/libs/aes/views/ItemView.js')
                ->registerScriptFile('/js/libs/aes/views/ModalView.js')
                ->registerScriptFile('/js/libs/aes/views/ButtonView.js')
                ->registerScriptFile('/js/libs/aes/views/NotificationsView.js');
    ?>
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
                var onSuccess = function(model) {
                        $('#register-elector').remove();

                        if(model.get('status') == ElectorRegistrationRequest.STATUS_REGISTERED) {
                            Aes.Notifications.add('You have been registered as elector.', 'success');
                        } else if(model.get('status') == ElectorRegistrationRequest.STATUS_AWAITING_ADMIN_DECISION) {
                            Aes.Notifications.add('Your registration request was sent. Election manager will consider it as soon as possible.', 'success');
                        }               

                        $('body').trigger('elector_registered', [model]);
                        parent.unmask();
                    },
                    registerClickHandler = function(){
                    
                        parent.mask();

                        var regReq = new ElectorRegistrationRequest({
                            user_id: <?= Yii::app()->user->id ?>,
                            election_id: <?= $this->election->id ?>
                        });

                        regReq.save({}, {
                            success: function(model) {
                                onSuccess(model);
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
                                                success: function(model) {
                                                    onSuccess(model);
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
        <?php } ?>
        
    <?php
    if (Yii::app()->user->checkAccess('election_selfAppointment', 
            array(
                'election' => $this->election,
                'candidate_user_id' => Yii::app()->user->id
            ))
    ) {
        
        $cs = Yii::app()->clientScript;
        $cs->registerPackage('aes-common')
                ->registerPackage('loadmask')
                ->registerScriptFile('/js/libs/aes/models/User.js')
                ->registerScriptFile('/js/libs/aes/models/Candidate.js')
                ->registerScriptFile('/js/libs/aes/views/ItemView.js')
                ->registerScriptFile('/js/libs/aes/views/NotificationsView.js');        
    ?>
        <div class="row-fluid">
            <button id="register-candidate" class="btn btn-large span8 offset2">Register as Candidate</button>
        </div>
        <script type="text/javascript">
            $(function() {
                var parent = $('#register-candidate').parent();
                var onSuccess = function(model) {
                    $('#register-candidate').remove();
                    
                    if(model.checkStatus('Registered')) {
                        Aes.Notifications.add('You have been registered as candidate.', 'success');
                    } else {
                        Aes.Notifications.add('Your registration request was sent. Election manager will consider it as soon as possible.', 'success');
                    }               
                    
                    $('body').trigger('candidate_registered', [model]);
                    parent.unmask();
                };
                
                $('#register-candidate').click(function(){
                    
                    parent.mask();
                    
                    var candidate = new Candidate({
                        user_id: <?= Yii::app()->user->id ?>,
                        election_id: <?= $this->election->id ?>
                    });
                    
                    candidate.save({}, {
                        success: function(model) {
                            onSuccess(model);
                        }
                    });
                });
            });
        </script>
        <?php } ?>        
    </div>
</div>

<?php
$this->endClip();

$this->beginContent('//layouts/core');
echo $content;
$this->endContent();
?>