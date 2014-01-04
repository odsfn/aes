<?php
if ($model->galleryBehavior->getGallery() === null) {
    echo '<p>Before add photos to product gallery, you need to save product</p>';
} else {
    $this->widget('GalleryManager', array(
        'gallery' => $model->galleryBehavior->getGallery(),
        'controllerRoute' => Yii::app()->createUrl('gallery'),
    ));
}
?>


<?php
$this->breadcrumbs->add(CHtml::encode($model->name));

$this->widget('PostsMarionetteWidget', array(
    'jsConstructorOptions' => array(
        'targetId' => $model->target_id,
        'targetType' => 'Election',
    ),                        
    'roleCheckParams' => array('election' => $model),        
    'checkForRoles' => array(
        'postsAdmin' => array('postsModeration', 
            function($widget) {
                return array(
                    'election' => $widget->roleCheckParams['election'],
                    'targetId' => $widget->jsConstructorOptions['targetId'],
                    'targetType' => $widget->jsConstructorOptions['targetType']
                );
            }
        )
    ),
    'show' => array('el' => '#posts-container')
));
?>

<div class="row-fluid">
    <div class="span12" id="posts-container"></div>
</div>



