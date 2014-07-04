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

?>

<div class="row-fluid">
    <div class="span12 actions">
    <?php
    if (Yii::app()->user->checkAccess('election_askToBecameElector', 
            array(
                'election' => $this->election,
                'elector_user_id' => Yii::app()->user->id
            ))
    ) {

        $cs = Yii::app()->clientScript;
        $cs->registerPackage('aes-common')
                ->registerPackage('loadmask')
                ->registerScriptFile('/js/libs/aes/models/User.js')
                ->registerScriptFile('/js/libs/aes/models/Elector.js');
    ?>
        <button id="register-elector" class="btn btn-large span8 offset2">Register as Elector</button>
        <script type="text/javascript">
            $(function() {
                var parent = $('#register-elector').parent();
                var onSuccess = function(model) {
                    $('#register-elector').remove();
                    parent.unmask();
                    $('body').trigger('elector_registered', [model]);
                };
                
                $('#register-elector').click(function(){
                    
                    parent.mask();
                    
                    var elector = new Elector({
                        user_id: <?= Yii::app()->user->id ?>,
                        election_id: <?= $this->election->id ?>
                    });
                    
                    elector.save({}, {
                        success: function(model) {
                            onSuccess(model);
                        }
                    });
                });
            });
        </script>
        <?php } ?>
    </div>
</div>

<?php
$this->endClip();

$this->beginContent('//layouts/core');
echo $content;
$this->endContent();
?>