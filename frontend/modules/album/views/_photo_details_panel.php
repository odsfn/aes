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
                <?php if ($canEdit): ?>
                <br>
                <b>Уровень доступа:</b>
                <?php 
                    echo $model->permissionLabel;
                    endif;
                ?>
                
            </p>
        </div>

        <div id="form-container">
            <input id="current-photo-href" type="hidden" 
                value="<?php 
                    $params = array(
                        'op'=>'view',
                        'photo_id'=>$model->id,
                        'exact'=>true
                    );
                    
                    if ($albumContext)
                        $params['album'] = $albumContext;
                    
                    echo $this->createUrl($this->getModule()->imageRoute, $params); 
                    ?>"/>
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
                if (Album::checkCoverAcceptance($albumContext, $model)) {
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
                    array('/album/image/photoDelete', 'photo_id' => $model->id),
                    array('class' => 'btn btn-danger btn-mini btn-block photo-delete')
                );
            }
            ?>
        </p>
    </div>
</div>