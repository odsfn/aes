<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 * 
 * @var Controller $this	
 */
$this->layout = 'application.views.layouts.column1';
?>
<h3>Log in</h3>

<?php 
$this->renderPartial('userAccount.views.login._form', array('model'=>$model));
?>