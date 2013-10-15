<?php

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'ElectionForm',
    'enableClientValidation'=>true,
    'type'=>'horizontal',
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    )
));

?>

<h3><?php echo Yii::t('profile', 'Create election'); ?></h3>

<p class="help-block"><?= Yii::t('common', 'Fields with {star} are required.', array('{star}'=>'<span class="required">*</span>')); ?></p>

<?php echo $form->errorSummary($model); ?>


<?php echo $form->fileFieldRow($model,'uploaded_file',array('class'=>'span6')); ?>

<?php echo $form->textFieldRow($model,'name',array('class'=>'span6')); ?>

<?php echo $form->textFieldRow($model,'mandate',array('class'=>'span6')); ?>

<?php echo $form->textFieldRow($model,'quote',array('class'=>'span6')); ?>

<?php echo $form->textFieldRow($model,'validity',array('class'=>'span6')); ?>


<?php echo $form->dropDownListRow($model, 'cand_reg_type', AESHelper::arrTranslated(Election::$cand_reg_types), array('class'=>'span6')); ?>

<?php echo $form->dropDownListRow($model, 'cand_reg_confirm', AESHelper::arrTranslated(Election::$cand_reg_confirms), array('class'=>'span6')); ?>

<?php echo $form->dropDownListRow($model, 'voter_reg_type', AESHelper::arrTranslated(Election::$voter_reg_types), array('class'=>'span6')); ?>

<?php echo $form->dropDownListRow($model, 'voter_reg_confirm', AESHelper::arrTranslated(Election::$voter_reg_confirms), array('class'=>'span6')); ?>


<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'primary',
        'label'=>'Save',
    )); ?>
</div>

<?php $this->endWidget(); ?>
