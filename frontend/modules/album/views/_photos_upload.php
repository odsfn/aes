<h3>Загрузка фотографий с вашего компьютера</h3>

<p>
Ограничения
Вы можете загрузить фотографии в форматах JPG, GIF или PNG
Вы можете загрузить до 50 фотографий за один раз.</p>

<?php
$this->widget('album.components.uploadify.MUploadify',array(

  // AR
  'model'=>$photo,
  'attribute'=>'filename',

  // CHTML
  //'name' => 'filename',
  'buttonText'=>'Загрузить',

  'uploader'=>$uploader,
  'auto'=>true,
  'multi'=>true,
  'method'=>'post',
  'fileTypeExts'=>'*.jpg;*.jpeg;*.gif;*.png',
  'fileTypeDesc'=>'Файлы изображений',
  'uploadButton'=>false,

  // Actions
  'onQueueComplete'=>"js:function(queueData) {document.location.replace('".$redirect."');}",
));
?>

<p>
Подсказка: чтобы выбрать несколько файлов, удерживайте нажатой
клавишу Ctrl во время выбора файлов в Windows или клавишу Cmd в Mac.</p>