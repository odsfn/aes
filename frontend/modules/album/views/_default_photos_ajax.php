<?php $this->renderPartial('/_images_listing', 
        compact(
            'photos', 'profile', 'nphotos',
            'photos_page', 'photos_per_page'
        )
); ?>