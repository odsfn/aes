<?php $this->renderPartial('/_images_listing', array_merge(
        compact(
            'photos', 'profile', 'nphotos',
            'photos_page', 'photos_per_page'
        ),
        array('album' => $model)
)); ?>