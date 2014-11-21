<h3>Альбомы</h3>
<?php $this->renderPartial('/_albums_listing', array(
    'albums' => $albums,
    'target_id' => $target_id,
    'nalbums' => $nalbums,
    'albums_page' => $albums_page,
    'albums_per_page' => $albums_per_page
)); ?>

<hr>
<h3>Фотографии
    <small class="pull-right"><ul class="nav nav-pills">
        <li class="<?php if (!$without_album) echo 'active'; ?>">
            <a href="<?= $this->createUrl($this->getModule()->albumRoute); ?>">Все</a>
        </li>
        <li class="<?php if ($without_album) echo 'active'; ?>">
            <a href="<?= $this->createUrl($this->getModule()->albumRoute, array('without_album'=>true)); ?>">Без альбома</a>
        </li>    
    </ul></small>
</h3>
<?php $this->renderPartial('/_images_listing', 
        compact(
            'photos', 'target_id', 'nphotos',
            'photos_page', 'photos_per_page', 'without_album'
        )
); ?>
