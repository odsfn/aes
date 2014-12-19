<?php $this->renderPartial('_items_listing', array_merge(
        compact(
            'gitems', 'target_id', 'ngitems',
            'gitems_page', 'gitems_per_page'
        ),
        array('album' => $model)
)); ?>