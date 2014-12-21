<?php
/** 
 * If you want to add some custom output to the end of galleryItemDetailsPanel you
 * should override this view by your own. And specify path to it in the module 
 * config attribute 'viewAfterGalleryItemDetails'
 * 
 * @var iGalleryItem $model
 */
$targetType = get_class($model);

$this->widget('frontend.widgets.CommentsWidget', array(
    'targetId' => $model->id,
    'targetType' => $targetType
));