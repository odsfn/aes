<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 * 
 * @var CController $this	
 */

Yii::app()->clientScript->registerCss('recovery', "div.captcha img {display: block; margin-bottom: 5px;}");

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'RecoveryForm',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
	'type'=>'vertical',
	'htmlOptions'=>array(
	    'class'=>'well span5 offset2'
	)
)); ?>
<h3>Reset password</h3>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model, 'email', array('class'=>'span5', 'maxlength'=>128)); ?>
	    
	<?php echo $form->textFieldRow($model, 'verifyCode', array('class'=>'span5', 'maxlength'=>128)); ?>
	<?php if(CCaptcha::checkRequirements()):?>
	<div class="captcha">
	    <? $this->widget('CCaptcha') ?>
	</div>
	<?php endif?>
	    

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'size'=>'large',
			'label'=>'Reset',
		)); ?>
</div>

<?php $this->endWidget(); ?>
<div class="clearfix"></div>
