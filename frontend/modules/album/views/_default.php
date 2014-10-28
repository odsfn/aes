<h3>Альбомы</h3>
<?php $this->renderPartial('/_albums_listing', array(
    'albums' => $albums,
    'target_id' => $target_id,
    'nalbums' => $nalbums,
    'albums_page' => $albums_page,
    'albums_per_page' => $albums_per_page
)); ?>

<hr>
<h3>Фотографии</h3>
<?php $this->renderPartial('/_images_listing', 
        compact(
            'photos', 'target_id', 'nphotos',
            'photos_page', 'photos_per_page'
        )
); ?>
