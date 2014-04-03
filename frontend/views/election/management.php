<?php

$this->breadcrumbs->add('Management', '/election/management/' . $model->id);
$this->breadcrumbs->add($model->name, '/election/view/' . $model->id);

$this->renderPartial('_form', array('model'=>$model));

?>

