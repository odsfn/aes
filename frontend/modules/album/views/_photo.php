<?php $this->widget('CLinkPager', array(
    'pages' => $pages,
    'maxButtonCount' => 0,
    'header' => '',
))?> <?php echo ($pages->currentPage + 1);?> из <?php echo $pages->itemCount;?>

<div class="row">
<?php 
echo CHtml::tag('img', 
    array('src'=> 
        ($model->path ? $this->getModule()->getComponent('image')->createAbsoluteUrl('600x480', $model->path) : $this->getModule()->getAssetsUrl('img/no_album.png'))
    )
); 
?>
</div>

<div align="left">

<div class="row-fluid">
  <div class="span7" style="text-align:left;">
      <p>Загружено <strong><?php echo $model->update?></strong></p>
      <p>
         <strong>Описание:</strong>&nbsp; <?php echo $model->description?><br/>
          <strong>Альбом:</strong>
            <?php echo (isset($model->album)? CHtml::link($model->album->name, array(
              $this->getModule()->albumRoute . '/op/view',
              'album_id' => $model->album->id
             )): '-Нет-')?>
          <br>
      </p>
  </div>

  <div class="span5" style="text-align:right;">
      <p></p>
      <p>
        <?php echo CHtml::link('Скачать', $this->getModule()->getComponent('image')->createAbsoluteUrl('original', $model->path) ); ?><br>
      </p>
  </div>
</div>

</div>