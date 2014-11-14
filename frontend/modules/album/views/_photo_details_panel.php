<div id="details-container" class="row-fluid">
    <div class="span10">
        <div id="details">
            <p class="description"><?php echo $model->description; ?></p>
            <p class="muted">
                <b>Загружено:</b> <?php echo Yii::app()->locale->dateFormatter->formatDateTime($model->update, 'medium', 'short'); ?>
                <br>
                <b>Альбом:</b>
                <?php echo (isset($model->album)? CHtml::link($model->album->name, array(
                  $this->getModule()->albumRoute . '/op/view',
                  'album_id' => $model->album->id
                 )): '-')?>
                <br>
                <b>Уровень доступа:</b> <?php echo $model->permissionLabel; ?>
            </p>
        </div>

        <div id="form-container">
            <input id="current-photo-href" type="hidden" value="<?= Yii::app()->request->url; ?>"/>
            <?php 
                if($canEdit) {
                    $this->renderPartial('/_photo_update', array(
                        'model' => $model,
                        'albumContext' => !empty($albumContext) ? $albumContext : false
                    )); 
                }
            ?>
        </div>
    </div>

    <div class="span2 text-right">
        <p>
            <?php 
            echo CHtml::link(
                'Скачать', 
                $this->getModule()->getComponent('image')->createAbsoluteUrl('original', $model->path),
                array('class' => 'btn btn-link btn-block')
            );

            if ($canEdit) {
                if ($albumContext 
                    && $model->album && !$model->album->isCover($model)
                ) {
                    echo CHtml::link(
                        'Назначить обложкой', 
                        array($this->getModule()->imageRoute . '/op/album', 'photo_id' => $model->id),
                        array(
                            'class' => 'btn btn-mini btn-block set-as-album-cover'
                        )
                    );
                }

                echo CHtml::button(
                    Yii::t('album', 'Редактировать'), 
                    array(
                        'id' => 'edit-photo',
                        'class' => 'btn btn-mini btn-block'
                    )
                );

                echo CHtml::link(
                    'Удалить', 
                    array($this->getModule()->imageRoute . '/op/delete', 'photo_id' => $model->id),
                    array('class' => 'btn btn-danger btn-mini btn-block')
                );
            }
            ?>
        </p>
    </div>
</div>