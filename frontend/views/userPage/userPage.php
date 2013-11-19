	
		<div id="user-info" class="row-fluid">
		    <div class="span12">
		    
			<h5 data-toggle="#personal-info"><?= Yii::t('userPage', 'Personal info'); ?>
                            <?php if($this->self): ?>
                            &nbsp;<small><a href="<?= Yii::app()->createUrl('/userAccount/profile/edit', array('#'=>'personal-info')); ?>"><?= Yii::t('userPage', 'Change'); ?></a></small>
                            <?php endif; ?>
                        </h5>

			<?php $this->widget('bootstrap.widgets.TbDetailView', array(
			    'htmlOptions' => array(
				'id' => 'personal-info'
			    ),
			    'type'  => 'condensed',
			    'data'  => $profile,
			    'attributes'=>array(
				'first_name', 'last_name', 'birth_place', 'birthDayFormated',
				'displayGender'
			    )
			)); ?>

			<h5 data-toggle="#contacts"><?= Yii::t('userPage', 'Contacts'); ?>
                            <?php if($this->self): ?>
                            &nbsp;<small><a href="<?= Yii::app()->createUrl('/userAccount/profile/edit', array('#'=>'contacts')); ?>"><?= Yii::t('userPage', 'Change'); ?></a></small>
                            <?php endif; ?>
                        </h5>

			<?php $this->widget('bootstrap.widgets.TbDetailView', array(
			    'htmlOptions' => array(
				'id' => 'contacts'
			    ),
			    'type'  => 'condensed',
			    'data'=>$profile,
			    'attributes'=>array(
				'email', 'mobile_phone'
			    ),
			)); ?>
			
		    </div>
		</div>
		

                <?php
                
                    $this->widget('PostsMarionetteWidget', array(
                        'jsConstructorOptions' => array(
                            'targetId'      => $profile->target_id,
                            'userPageId'    => $profile->user_id,
                            'limit'         => (defined('TEST_APP_INSTANCE') && TEST_APP_INSTANCE) ? 3 : 20
                        ),
                        'show' => array('div', array('id' => 'posts', 'class' => 'row-fluid'))
                    ));
                ?>