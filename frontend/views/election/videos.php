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

if(Yii::app()->request->getParam('action') == 'ViewGalleryItem'
    && !Yii::app()->request->isAjaxRequest) {
    $this->createWidget('CommentsMarionetteWidget')->register();
}

$this->breadcrumbs->add('Videos', 'election/videos/' . $this->election->id);

echo $galleryWidgetOutput;