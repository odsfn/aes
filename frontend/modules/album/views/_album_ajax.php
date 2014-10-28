<?php $this->renderPartial('/_images_listing', array_merge(
        compact(
            'photos', 'target_id', 'nphotos',
            'photos_page', 'photos_per_page'
        ),
        array('album' => $model)
)); ?>