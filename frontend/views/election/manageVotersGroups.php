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
        
        <iframe id="ElectoralGroups" src="http://aes.dev/ui/ext/ElectoralGroups/index.html" class="ext-app">
            Something will be loaded here
        </iframe>
        
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