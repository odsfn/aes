<?php 
$this->layout = '//layouts/user';

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'RegistrationForm',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
	'type'=>'horizontal',
	'htmlOptions' => array(
	    'enctype' => 'multipart/form-data',
	)
)); ?>

<h3><?= Yii::t('profile', 'Profile settings'); ?></h3>

<p class="help-block"><?= Yii::t('common', 'Fields with {star} are required.', array('{star}'=>'<span class="required">*</span>')); ?></p>

<?php echo $form->errorSummary($model); ?>

	<h5><?= Yii::t('userPage', 'Personal information'); ?><a href="#personal-info"></a></h5><hr>

	<?php echo $form->fileFieldRow($model,'uploadingPhoto',array('class'=>'span6')); ?>
	
	<?php echo $form->textFieldRow($model,'first_name',array('class'=>'span6')); ?>

	<?php echo $form->textFieldRow($model,'last_name',array('class'=>'span6')); ?>

	<?php echo $form->textFieldRow($model,'birth_place',array('class'=>'span6')); ?>

        <?php echo $form->datepickerRow($model, 'birthDayFormated',
	    array('prepend'=>'<i class="icon-calendar"></i>','class'=>'span6')); ?>

	<?php echo $form->dropDownListRow($model, 'gender',
	    array('', '1' => 'Male', '2' => 'Famale'), array('class'=>'span6')); 
	?>

	<h5><?= Yii::t('userPage', 'Contacts'); ?><a href="#contacts"></a></h5><hr>
	
	<?php echo $form->maskedTextFieldRow($model,'mobile_phone', '+9(999)999-99-99', array('class'=>'span6', 'maxlength'=>18)); ?>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'Save',
		)); ?>
</div>

<?php $this->endWidget(); ?>
