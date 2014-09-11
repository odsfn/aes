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
            $src = Yii::app()->getBaseUrl(true) . '/ui/ext/';
            if (!defined('YII_DEBUG') || YII_DEBUG == false)
                $src .= 'build/production/';
            $src .= 'ElectoralGroups/index.html';
        ?>
        <iframe id="ElectoralGroups" src="<?= $src ?>" class="ext-app"></iframe>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('iframe#ElectoralGroups').get(0).contentWindow.appConfig = {
        userId: <?= Yii::app()->user->id; ?>,
        electionId: <?= $model->id ?>
    };
});
</script>