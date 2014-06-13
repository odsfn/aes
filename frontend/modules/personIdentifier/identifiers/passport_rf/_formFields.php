<?php    
    echo $form->textFieldRow($model, 'serial', array('class'=>'span12','maxlength'=>128));
    echo $form->textFieldRow($model, 'number', array('class'=>'span12','maxlength'=>128));
    echo $form->datepickerRow($model, 'issued', array('prepend'=>'<i class="icon-calendar"></i>', 'class'=>'span8'));
    echo $form->textAreaRow($model, 'issuer', array('class'=>'span12'));
?>
