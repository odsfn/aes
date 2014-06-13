<?php
    foreach ($model->typeAttributeNames as $attribute) {
        echo $form->textFieldRow($model, $attribute, array('class'=>'span12','maxlength'=>128)); 
    }
?>