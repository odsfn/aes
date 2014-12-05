<?php $this->renderPartial($this->getAction()->viewItemsListing,
        compact(
            'photos', 'target_id', 'nphotos',
            'photos_page', 'photos_per_page', 'without_album'
        )
); ?>