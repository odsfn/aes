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

<p class="help-block"><?= Yii::t('common', 'Fields with {star} are required.', array('{star}'=>'<span class="required">*</span>')); ?></p>

<?php echo $form->errorSummary($model); ?>


<?php echo $form->fileFieldRow($model,'uploaded_file',array('class'=>'span6')); ?>

<?php echo $form->textFieldRow($model,'name',array('class'=>'span6')); ?>

<h5 data-toggle="#mandate"><?= Yii::t('election.provisions', 'Mandate'); ?><a name="mandate"></a></h5><hr>

<?php echo $form->textFieldRow($model,'mandate',array('class'=>'span6')); ?>

<?php echo $form->textFieldRow($model,'quote',array('class'=>'span6')); ?>

<?php echo $form->textFieldRow($model,'validity',array('class'=>'span6')); ?>

<h5 data-toggle="#candidate-registration-options"><a name="candidate-registration-options"></a>
    <?= Yii::t('election.provisions', 'Candidate registraion options'); ?>
</h5><hr>

<?php echo $form->dropDownListRow($model, 'cand_reg_type', AESHelper::arrTranslated(Election::$cand_reg_types), array('class'=>'span6')); ?>

<?php echo $form->dropDownListRow($model, 'cand_reg_confirm', AESHelper::arrTranslated(Election::$cand_reg_confirms), array('class'=>'span6')); ?>

<h5 data-toggle="#electorate-registration-options"><a name="electorate-registration-options"></a>
    <?= Yii::t('election.provisions', 'Electorate registraion options'); ?>
</h5><hr>

<?php echo $form->dropDownListRow($model, 'voter_reg_type', AESHelper::arrTranslated(Election::$voter_reg_types), array('class'=>'span6')); ?>

<?php echo $form->dropDownListRow($model, 'voter_reg_confirm', AESHelper::arrTranslated(Election::$voter_reg_confirms), array('class'=>'span6')); ?>

<h5 data-toggle="#revote-options"><a name="revote-options"></a>
    <?= Yii::t('election.provisions', 'Revote options'); ?>
</h5><hr>

<?php  
    echo $form->textFieldRow($model,'revotes_count',array('class'=>'span6'));
    echo $form->textFieldRow($model,'remove_vote_time',array('class'=>'span6'));
    echo $form->textFieldRow($model,'revote_time',array('class'=>'span6'));
?>

<?php if(!$model->isNewRecord): ?>

<h5 data-toggle="#status"><a name="status"></a>
    <?= Yii::t('election', 'Status'); ?>
</h5><hr>

<?php echo $form->dropDownListRow($model, 'status', AESHelper::arrTranslated($model->getAvailableStatuses()), array('class'=>'span6')); ?>

<?php endif; ?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'primary',
        'label'=>'Save',
    )); ?>
</div>

<?php $this->endWidget(); ?>
<script type="text/javascript">
    $(function(){
        var handler = function() {
            if($('#Election_voter_reg_type').val() == 1)
                $('#Election_voter_reg_confirm').val(0).prop("disabled", true);
            else
                $('#Election_voter_reg_confirm').prop('disabled', false);
        };
        $('#Election_voter_reg_type').on('change', handler);
        handler();
    });
</script>