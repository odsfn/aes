<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'petition-form',
    'enableAjaxValidation'  => false,
    'enableClientValidation'=> true
)); ?>

<p class="help-block">Fields with <span class="required">*</span> are required.</p>

<?php echo $form->errorSummary($model); ?>

    <?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>1024)); ?>

    <?php echo $form->textAreaRow($model,'content',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

    <?php echo $form->hiddenField($model,'mandate_id'); ?>

    <?php echo $form->hiddenField($model,'creator_id'); ?>

    <?php // echo $form->textFieldRow($model,'mandate_id',array('class'=>'span5')); ?>

    <?php // echo $form->textFieldRow($model,'creator_id',array('class'=>'span5')); ?>

<div class="form-actions">
    <?php
        $btnText = $model->isNewRecord ? Yii::t('aes', 'Create') : Yii::t('aes', 'Save');
    
        if (empty($forAjax)) {
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>$btnText,
            )); 
        } else {
            echo CHtml::ajaxSubmitButton($btnText, array('petition/ajaxCreate'), 
                array(
                    'dataType'=> 'json',
                    'success' => 'function(response) {
                        if(response.success === true) {
                            $("body").trigger("petitionCreated", [response]);
                        } else {
                            $("body").trigger("petitionCreationFailed", [response]);
                        }
                    }'
                ), 
                array(
                    'class' => 'btn btn-primary'
                )
            );
        }
    ?>
</div>

<?php $this->endWidget(); ?>