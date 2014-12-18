<h3>Альбомы</h3>
<?php $this->render('_albums_listing', array(
    'albums' => $albums,
    'target_id' => $target_id,
    'nalbums' => $nalbums,
    'albums_page' => $albums_page,
    'albums_per_page' => $albums_per_page
)); ?>

<hr>
<h3><?php echo Yii::t('album.messages', $this->getAction()->pluralLabel); ?>
    <small class="pull-right"><ul class="nav nav-pills">
        <li class="<?php if (!$without_album) echo 'active'; ?>">
            <a href="<?= $this->createUrl($this->getModule()->rootRoute); ?>">Все</a>
        </li>
        <li class="<?php if ($without_album) echo 'active'; ?>">
            <a href="<?= $this->createUrl($this->getModule()->rootRoute, array('without_album'=>true)); ?>">Без альбома</a>
        </li>    
    </ul></small>
</h3>
<?php $this->renderPartial($this->getAction()->viewItemsListing, 
        compact(
            'photos', 'target_id', 'nphotos',
            'photos_page', 'photos_per_page', 'without_album'
        )
); ?>
