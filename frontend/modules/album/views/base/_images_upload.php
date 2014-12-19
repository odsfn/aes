<?php 
/**
 * Batch Images Upload page
 */

Yii::app()->clientScript
    ->registerCssFile($this->getModule()->getAssetsUrl('css/thumbnails.css'))
    ->registerScriptFile($this->getModule()->getAssetsUrl('js/gitemsUpload.js'))
    ->registerScriptFile($this->getModule()->getAssetsUrl('js/gitemUpdateCompact.js'));
?>

<script type="text/javascript">
$(function(){
    $(document)
        .unbind('ajaxStart')
        .unbind('ajaxStop');
});
</script>
<h3>Загрузка фотографий с вашего компьютера</h3>

<p>
    <i>Ограничения:</i>
    Вы можете загрузить фотографии в форматах JPG, GIF или PNG
    Вы можете загрузить до 50 фотографий за один раз.
</p>

<p>
    <i>Подсказка:</i> чтобы выбрать несколько файлов, удерживайте нажатой
    клавишу Ctrl во время выбора файлов в Windows или клавишу Cmd в Mac.
</p>

<div class="row-fluid">
    <div id="uploaded-gitems-container" class="span8"></div>
    
    <div id="uploading-queue-container" class="span4">
    <?php
    $this->widget('album.components.uploadify.MUploadify',array(

        // AR
        'model'=>$gitem,
        'attribute'=>'filename',

        // CHTML
        //'name' => 'filename',
        'buttonText'=>Yii::t('album.messages', 'Загрузить'),

        'uploader'=>$uploader,
        'auto'=>true,
        'multi'=>true,
        'method'=>'post',
        'fileTypeExts' => '*.jpg;*.jpeg;*.gif;*.png',
        'fileTypeDesc' => 'Файлы изображений',
        'fileSizeLimit' => $this->getModule()->imageSizeLimit,
        'uploadButton'=>false,

        // Actions
        'onUploadSuccess'=>"js:function(file, data, response) {handleUploaded(file, data, response)}"
    ));
    ?>
    </div>
</div>