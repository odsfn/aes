<?php $this->renderPartial($this->getAction()->viewItemsListing,
        compact(
            'gitems', 'target_id', 'ngitems',
            'gitems_page', 'gitems_per_page', 'without_album'
        )
); ?>