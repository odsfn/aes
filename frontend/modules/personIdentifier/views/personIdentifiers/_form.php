<?php
$module = Yii::app()->getModule('personIdentifier');

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

Yii::app()->clientScript->registerCss('personIdentifierStyles', 
        "input[type=\"file\"] {"
            . "margin-bottom: 10px;"
        . "}"
        . ".popover {"
            . "max-width: " . ($module->identifierExampleMaxWidth + 5) . "px;"
            . "max-height: " . ($module->identifierExampleMaxHeight + 5) . "px;"
        . "}"
        . ".popover-content {"
            . "padding: 2px;"
        . "}"
        . ".popover-content img {"
            . "max-width: {$module->identifierExampleMaxWidth}px;"
            . "max-height: {$module->identifierExampleMaxHeight}px;"
        . "}");

?>
<script type="text/javascript">
    $('#PersonIdentifier_type').change(function() {
        $('#identifier-input-container').block({
            message: null,
            overlayCSS:  { 
                backgroundColor: '#f5f5f5', 
                opacity: 0.3, 
                cursor: 'wait'
            }
        });
        $('#identifier-input-container').smartLoad(
            '<?= $this->owner->createUrl('/personIdentifier/types/getFormAttrs/'); ?>',
            {type: this.value},
            function() {
                $('#identifier-input-container').unblock();
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
