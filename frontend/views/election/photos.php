<script type="text/javascript">
$(function(){
    $(document).ajaxStart(function(){
        $('#column-right').mask('Loading...');
    });
    $(document).ajaxStop(function(){
        $('#column-right').unmask();
    });
});
</script>
<?php
Yii::app()->clientScript->registerPackage('loadmask');
$this->breadcrumbs->add('Photos', 'userPage/photos/' . $profile->user_id);

$this->renderClip('album');