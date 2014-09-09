<?php

$this->breadcrumbs->add('Elections', '/election');

Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/layouts/election.css');

$this->clips['titleIcon'] = '<i class="icon-briefcase"></i>';

$this->beginClip('titleAfterBredcrumbsContent');?>

<h3 class="pull-right"><small><?php echo $this->election->text_status; ?></small></h3>

<?php 
$this->endClip();

$this->beginContent('//layouts/fullwidth');
echo $content;
$this->endContent();
?>

