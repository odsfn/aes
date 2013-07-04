<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 * @var $this IdentityController
 */
?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'EditIdentityForm',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
	'type'=>'vertical',
	'htmlOptions'=>array(
	    'class'=>'well span5 offset2'
	)
)); ?>

<h3>Change your identity data</h3>

<p class="help-block">Fields with <span class="required">*</span> are required.</p>
<p></p>

	<?php echo $form->errorSummary(array($identity, $newPassword)); ?>

	<?php 
	    echo $form->textFieldRow($identity, 'identity', array('class'=>'span5', 'maxlength'=>128));
	    
	    echo $form->passwordFieldRow($newPassword, 'password', array('class'=>'span5', 'maxlength'=>128));
	    echo $form->passwordFieldRow($newPassword, 'password_check', array('class'=>'span5', 'maxlength'=>128));
	?>
<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'size'=>'large',
			'label'=>'Change',
		)); ?>
</div>

<?php $this->endWidget(); ?>

<div class="clearfix"></div>
