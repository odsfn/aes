<h5>Person identifier</h5>

<?php 

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'person-identifier',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'type'=>'vertical',
	'htmlOptions'=>array(
	    'class'=>'well span5 offset2'
	)
));  

echo $form->errorSummary($model);

$this->renderPartial('/personIdentifiers/_formFields', array('model' => $model, 'form' => $form));

$this->endWidget(); 

?>
