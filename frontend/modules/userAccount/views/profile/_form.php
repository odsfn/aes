<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'RegistrationForm',
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>false,
	'type'=>'vertical',
	'htmlOptions'=>array(
	    'class'=>'well span5 offset2'
	)
)); ?>

<h3>Registration</h3>

<p class="help-block">Fields with <span class="required">*</span> are required.</p>
<p></p>

<?php echo $form->errorSummary($model); ?>

	<?php 
	    echo $form->passwordFieldRow($model, 'password', array('class'=>'span5', 'maxlength'=>128));
	    
	    echo $form->passwordFieldRow($model, 'password_check', array('class'=>'span5', 'maxlength'=>128));
	?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span5', 'maxlength'=>128)); ?>

	<?php echo $form->maskedTextFieldRow($model,'mobile_phone', '+9(999)999-99-99', array('class'=>'span5', 'maxlength'=>18)); ?>

	<?php echo $form->textFieldRow($model,'first_name',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->textFieldRow($model,'last_name',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->textFieldRow($model,'birth_place',array('class'=>'span5','maxlength'=>128)); ?>

        <?php echo $form->datepickerRow($model, 'birth_day',
	    array('prepend'=>'<i class="icon-calendar"></i>')); ?>

	<?php echo $form->dropDownListRow($model, 'gender',
	    array('', '1' => 'Male', '2' => 'Famale')); 
	?>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'size'=>'large',
			'label'=>'Create Account',
		)); ?>
</div>

<?php $this->endWidget(); ?>
