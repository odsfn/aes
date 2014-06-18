<?php

if (empty($form)) {
    $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
            'id'=>'person-identifier',
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'type'=>'vertical',
            'htmlOptions'=>array(
                'enctype' => 'multipart/form-data',
            )
    ));  
    
    $shouldCloseWidget = true;
    
    echo $form->errorSummary($model);
}

echo $form->dropDownListRow($model, 'type', PersonIdentifier::getTypesCaptions(), array('class'=>'span12'));

?>
<script type="text/javascript">
    $('#PersonIdentifier_type').change(function() {
        $.maskElement($('#identifier-input-container'), 'Loading...');
        $('#identifier-input-container').smartLoad(
            '<?= $this->owner->createUrl('/personIdentifier/types/getFormAttrs/'); ?>',
            {type: this.value},
            function() {
                $.unmaskElement($(this));
            }
        );
    });
</script>

<?php echo $form->fileFieldRow($model,'uploadingImage',array('class'=>'span12')); ?>

<div id='person-identifier-fields'>
    <?php $this->owner->renderPartial($fieldsView, array('model' => $model, 'form' => $form)); ?>
</div>    

<?php
if ($shouldCloseWidget)
    $this->endWidget(); 
?>
