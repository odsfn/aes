<?php $this->renderPartial('/_images_listing', 
        compact(
            'photos', 'target_id', 'nphotos',
            'photos_page', 'photos_per_page'
        )
); ?>