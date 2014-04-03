<?php

$this->breadcrumbs->add('Management', '/election/management/' . $model->id);
$this->breadcrumbs->add($model->name, '/election/view/' . $model->id);

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'ElectionForm',
    'enableClientValidation'=>true,
    'type'=>'horizontal',
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    )
));

?>

<h3><?php echo Yii::t('election', 'Manage election'); ?></h3>

<?php echo $form->errorSummary($model); ?>

<?php echo $form->dropDownListRow($model, 'status', AESHelper::arrTranslated(Election::$statuses), array('class'=>'span6')); ?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'primary',
        'label'=>'Save',
    )); ?>
</div>

<?php $this->endWidget(); ?>
