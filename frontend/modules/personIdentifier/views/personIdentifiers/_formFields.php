
<?php echo $form->fileFieldRow($model,'uploadingImage',array('class'=>'span12')); ?>

<?php 

$types = array_combine(PersonIdentifier::getTypes(), PersonIdentifier::getTypes());

echo $form->dropDownListRow($model, 'type', $types, array(
    'class'=>'span12',
    'ajax'=>array(
        'type' => 'GET',
        'url'  => $this->createUrl('/personIdentifier/types/getFormAttrs/'),
        'update' => '#person-identifier-fields',
        'data' => array('type' => 'js:this.value'),
        'success' => 'js:function(html){jQuery("#person-identifier-fields").html(jQuery(html).find("#person-identifier-fields").html())}'
    )
)); 
?>

<div id='person-identifier-fields'>
<?php
    foreach ($model->typeAttributeNames as $attribute) {
        echo $form->textFieldRow($model, $attribute, array('class'=>'span12','maxlength'=>128)); 
    }
?>
</div>