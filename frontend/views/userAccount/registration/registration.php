<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 * 
 * @var $this	Controller
 */
$this->layout = '//layouts/column1';
?>
<h3>Registration</h3>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'verticalForm',
    'htmlOptions'=>array('class'=>'well'),
)); ?>
 
<?php echo $form->textFieldRow($model, 'textField', array('class'=>'span3')); ?>
<?php echo $form->passwordFieldRow($model, 'password', array('class'=>'span3')); ?>
<?php echo $form->checkboxRow($model, 'checkbox'); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Login')); ?>
 
<?php $this->endWidget(); ?>