<?php $this->renderPartial('/_albums_listing', array(
    'albums' => $albums,
    'target_id' => $target_id,
    'nalbums' => $nalbums,
    'albums_page' => $albums_page,
    'albums_per_page' => $albums_per_page
)); ?>