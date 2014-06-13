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

echo $form->fileFieldRow($model,'uploadingImage',array('class'=>'span12'));

$types = array_combine(PersonIdentifier::getTypes(), PersonIdentifier::getTypes());

echo $form->dropDownListRow($model, 'type', $types, array('class'=>'span12'));

?>
<script type="text/javascript">
    $('#PersonIdentifier_type').change(function() {
        $('#identifier-input-container').css('cursor', 'wait');
        $('#identifier-input-container').load(
            '<?= $this->owner->createUrl('/personIdentifier/types/getFormAttrs/'); ?>',
            {type: this.value},
            function() {
                $('#identifier-input-container').css('cursor', 'default');
            }
        );
    });
</script>
<div id='person-identifier-fields'>
    <?php $this->owner->renderPartial($fieldsView, array('model' => $model, 'form' => $form)); ?>
</div>    

<?php
if ($shouldCloseWidget)
    $this->endWidget(); 
?>
