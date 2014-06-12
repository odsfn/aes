<?php
/**
 * Customized registration page
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 * @var Controller $this	
 */

$this->renderPartial($this->module->registrationFormView, array(
    'model' => $model,
    'personIdent' => $personIdent
));
?>
<div class="clearfix"></div>