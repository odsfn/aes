<?php

$this->breadcrumbs->add('Elections', '/election');

Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/layouts/election.css');

$this->clips['titleIcon'] = '<i class="icon-briefcase"></i>';

$this->beginClip('titleAfterBredcrumbsContent');?>

<h3 class="pull-right"><small><?php echo $this->election->text_status; ?></small></h3>

<?php 
$this->endClip();

$this->beginClip('mainPicture');

if($this->election->have_pic) {
    $this->widget('common.widgets.imageWrapper.ImageWrapper', array(
        'imageSrc' => $this->election->picUrl,                                
        'width' => '20%'
    ));
}

$this->endClip(); 


$this->beginClip('navigation');

$this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'pills',
    'stacked' => 'true',
    'items' => array(
        array('label'=> Yii::t('election', 'Election page'), 'url'=> array('/election/view', 'id'=>$this->election->id)),
        array('label'=> Yii::t('election', 'Provisions'), 'url'=> array('/election/provisions', 'id'=>$this->election->id)),
        array('label'=> Yii::t('election', 'Candidates'), 'url'=> array('/election/candidates', 'id'=>$this->election->id)),
        array('label'=> Yii::t('election', 'Electorate'), 'url'=> array('/election/electorate', 'id'=>$this->election->id)),
        array('label'=> Yii::t('election', 'Admins'), 'url'=> array('/election/admins', 'id'=>$this->election->id)),
        array('label'=> Yii::t('election', 'Management'), 'url'=> array('/election/management', 'id'=>$this->election->id), 'visible' => Yii::app()->user->checkAccess('election_administration', array('election' => $this->election)))
    )
));

$this->endClip();

$this->beginContent('//layouts/core');
echo $content;
$this->endContent();
?>