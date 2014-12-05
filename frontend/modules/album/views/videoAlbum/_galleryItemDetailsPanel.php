<div id="details-container" class="row-fluid">
    <div class="span10">
        <div id="details">
            <p class="description"><?php echo $model->description; ?></p>
            <p class="muted">
                <b>Загружено:</b> <?php echo Yii::app()->locale->dateFormatter->formatDateTime($model->update, 'medium', 'short'); ?>
                <br>
                <b>Альбом:</b>
                <?php echo (isset($model->album)? CHtml::link($model->album->name, array(
                  $this->getModule()->rootRoute,
                  'action' => 'ViewAlbum',
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
                        'action'=>'ViewGalleryItem',
                        'photo_id'=>$model->id,
                        'exact'=>true
                    );
                    
                    if ($albumContext)
                        $params['album'] = $albumContext;
                    
                    echo $this->createUrl($this->getModule()->rootRoute, $params); 
                    ?>"/>
            <?php 
                if($canEdit) {
                    $this->renderPartial($this->getAction()->viewUpdateGalleryItem, array(
                        'model' => $model,
                        'albumContext' => !empty($albumContext) ? $albumContext : false,
                        'action' => $this->owner->createAbsoluteUrl(
                            $this->getModule()->rootRoute, 
                            array(
                                'action' => 'UpdateGalleryItem',
                                'photo_id' => $model->id, 
                                'albumContext' => !empty($albumContext) ? $albumContext : false
                            )
                        ),
                        'showCancelBtn' => true,
                        'target_id' => $target_id
                    )); 
                }
            ?>
        </div>
    </div>

    <div class="span2 text-right">
        <p>
            <?php 

            if ($canEdit) {
                
                $albumType = $this->getAction()->albumType;
                
                if ($albumType::checkCoverAcceptance($albumContext, $model)) {
                    echo CHtml::link(
                        'Назначить обложкой', 
                        array(
                            $this->getModule()->rootRoute, 
                            'action' => 'SetAlbumCover',
                            'photo_id' => $model->id
                        ),
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
                    array(
                        $this->getModule()->rootRoute,
                        'action' => 'DeleteGalleryItem',
                        'photo_id' => $model->id
                    ),
                    array('class' => 'btn btn-danger btn-mini btn-block photo-delete')
                );
            }
            ?>
        </p>
    </div>
</div>