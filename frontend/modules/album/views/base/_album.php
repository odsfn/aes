<?php if($model->description): ?> 
<h3><?= Yii::t('album', 'Описание альбома'); ?></h3>
<p><?= $model->description; ?></p> 
<?php endif; ?>
<h3><?php echo $this->getAction()->pluralLabel; ?> <small>В альбоме <?php echo $ngitems; ?> записей</small></h3>
<?php $this->renderPartial('_items_listing', array_merge(
        compact(
            'gitems', 'target_id', 'ngitems',
            'gitems_page', 'gitems_per_page'
        ),
        array('album' => $model)
)); ?>