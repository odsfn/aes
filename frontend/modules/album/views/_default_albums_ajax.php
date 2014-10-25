<?php $this->renderPartial('/_albums_listing', array(
    'albums' => $albums,
    'profile' => $profile,
    'nalbums' => $nalbums,
    'albums_page' => $albums_page,
    'albums_per_page' => $albums_per_page
)); ?>