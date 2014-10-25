<h3>Альбомы</h3>
<?php $this->renderPartial('/_albums_listing', array(
    'albums' => $albums,
    'profile' => $profile,
    'nalbums' => $nalbums,
    'albums_page' => $albums_page,
    'albums_per_page' => $albums_per_page
)); ?>

<hr>
<h3>Фотографии</h3>
<?php $this->renderPartial('/_images_listing', 
        compact(
            'photos', 'profile', 'nphotos',
            'photos_page', 'photos_per_page'
        )
); ?>
