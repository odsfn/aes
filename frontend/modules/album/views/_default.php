<h2>Альбомы</h2>
<div id="replace_albums_container">
  <?php if($albums):?>
  <ul class="gallery">
  <?php foreach($albums as $album):?>
    <li>
      <?php echo CHtml::link($album->name, array($this->getModule()->albumRoute . '/op/view', 'album_id' => $album->id)); ?><br>
      Обновлен: <?php echo $album->update; ?><br>
      <?php echo CHtml::link( CHtml::tag('img', array('src'=> ($album->path? $this->getModule()->getComponent('image')->createAbsoluteUrl('100x100', $album->path): $this->getModule()->getAssetsUrl('img/no_album.png')))), array(
          $this->getModule()->albumRoute . '/op/view',
          'album_id' => $album->id,
          'profile' => $profile->user_id,
        )); ?><br>
      <?php //echo $album->path; ?>
    </li>
  <?php endforeach; ?>
  </ul>
  <?php else:?>
    Фотоальбомы отсутствуют
  <?php endif;?>

  <br clear="all">

  <?php if($nalbums > $albums_page * $albums_per_page):?>
  <div align="center"><?php echo CHtml::ajaxlink('Показать больше записей', array(
      $this->getModule()->albumRoute,
      'albums_page'=>++$albums_page,
      'view' => 'albums',
      'profile' => $profile->user_id,
    ), array('replace'=>'#replace_albums_container'), array('live'=>false, 'id' => 'send-link-'.uniqid()));?>
  </div>
  <?php endif;?>

</div>

<h2>Фотографии</h2>
<div id="replace_photos_container">
  <?php if($photos):?>
  <ul class="gallery">
  <?php foreach($photos as $page => $photo):?>
    <li>
    <?php 
    echo CHtml::link( 
        CHtml::tag('img', array(
            'src'=> ($photo->path ? $this->getModule()->getComponent('image')->createAbsoluteUrl('100x100', $photo->path): $this->getModule()->getAssetsUrl('img/no_album.png'))
        )), 
        array(
            $this->getModule()->imageRoute . '/op/view',
            'photo_id' => $photo->id,
            'profile' => $profile->user_id,
            'page' => ++$page,
        )
    ); 
    ?>
    </li>
  <?php endforeach; ?>
  </ul>
  <?php else:?>
    Фотографии отсутствуют
  <?php endif;?>

  <br clear="all">

  <?php if($nphotos > $photos_page * $photos_per_page):?>
  <div align="center"><?php echo CHtml::ajaxlink('Показать больше записей', array(
      $this->getModule()->albumRoute,
      'photos_page'=>++$photos_page,
      'view' => 'photos',
      'profile' => $profile->user_id,
    ), array('replace'=>'#replace_photos_container'), array('live'=>false, 'id' => 'send-link-'.uniqid()));?>
  </div>
  <?php endif;?>

</div>
