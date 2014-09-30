<?php 
    $this->breadcrumbs->add('Global Voter Groups Management', '');
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
            
            $src .= 'GlobalVoterGroups/index.html';
        ?>
        <iframe id="ElectoralGroups" src="<?= $src ?>" class="ext-app"></iframe>
    </div>
</div>
<script type="text/javascript">
    $('#voters-groups-cntr').mask('Loading...');
    
    $('iframe#ElectoralGroups').get(0).contentWindow.appConfig = {
        userId: <?= Yii::app()->user->id; ?>,
        baseUrl: "<?= $baseUrl ?>",
        onLaunch: function() {
            $('#voters-groups-cntr').unmask();
        }
    };

</script>