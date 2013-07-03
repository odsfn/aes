<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'LoginForm',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
	'type'=>'vertical',
	'htmlOptions'=>array(
	    'class'=>'well span5 offset2'
	)
)); ?>
<h3>Log in</h3>
<p class="help-block">Fields with <span class="required">*</span> are required.</p>
<p></p>

	<?php echo $form->errorSummary($model); ?>

	<?php 
	    echo $form->textFieldRow($model, 'identity', array('class'=>'span5', 'maxlength'=>128));
	    
	    echo $form->passwordFieldRow($model, 'password', array('class'=>'span5', 'maxlength'=>128));
	    
	    echo $form->checkboxRow($model, 'rememberMe'); 
	?>
<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'size'=>'large',
			'label'=>'Log in',
		)); ?>
    <a href="<?= Yii::app()->getModule('userAccount')->recoveryUrl; ?>" class="pull-right">Lost password?</a>
</div>

<?php $this->endWidget(); ?>
<div class="clearfix"></div>