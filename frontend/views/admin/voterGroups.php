<?php 
    $this->breadcrumbs->add('Global Voter Groups Management', '');
?>
<div class="row-fluid">
    <div id="voters-groups-cntr" class="span12">
        <?php 
            $src = Yii::app()->getBaseUrl(true) . '/ui/ext/';
            $baseUrl = '/';
            
            if (!defined('YII_DEBUG') || YII_DEBUG == false) {
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

    $('iframe#ElectoralGroups').get(0).contentWindow.appConfig = {
        userId: <?= Yii::app()->user->id; ?>,
        baseUrl: "<?= $baseUrl ?>"
    };

</script>