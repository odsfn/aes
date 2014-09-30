<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
$this->layout = '//layouts/election-fullwidth';

$this->breadcrumbs->add('Manage Voters Groups', '/election/manageVotersGroups/' . $model->id);
$this->breadcrumbs->add($model->name, '/election/view/' . $model->id);

?>

<div class="row-fluid">
    <div id="voters-groups-cntr" class="span12">
        <?php 
            Yii::app()->clientScript->registerPackage('loadmask');
            
            $src = Yii::app()->getBaseUrl(true) . '/ui/ext/';
            $baseUrl = '/';
            
            if (Yii::app()->params['ext_debug'] == false) {
                $src .= 'build/production/';
            }
            
            if(defined('TEST_APP_INSTANCE'))
                $baseUrl = '/index-test.php/';
            
            $src .= 'ElectoralGroups/index.html';
        ?>
        <iframe id="ElectoralGroups" src="<?= $src ?>" class="ext-app"></iframe>
    </div>
</div>
<script type="text/javascript">
    $('#voters-groups-cntr').mask('Loading...');
    
    $('iframe#ElectoralGroups').get(0).contentWindow.appConfig = {
        userId: <?= Yii::app()->user->id; ?>,
        electionId: <?= $model->id ?>,
        election: <?= CJavaScript::encode($model->getAttributes()) ?>,
        baseUrl: "<?= $baseUrl ?>",
        onLaunch: function() {
            $('#voters-groups-cntr').unmask();
        }
    };

</script>