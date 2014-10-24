<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'file-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'user_id'); ?>
        <?php echo $form->textField($model,'user_id'); ?>
        <?php echo $form->error($model,'user_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'album_id'); ?>
        <?php echo $form->textField($model,'album_id'); ?>
        <?php echo $form->error($model,'album_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'filename'); ?>
        <?php echo $form->textField($model,'filename',array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->error($model,'filename'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'path'); ?>
        <?php echo $form->textField($model,'path',array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->error($model,'path'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'update'); ?>
        <?php echo $form->textField($model,'update'); ?>
        <?php echo $form->error($model,'update'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->