<div id="replace_photos_container">
    <?php if ($photos): ?>
        <ul class="thumbnails gallery">
            <?php
            foreach ($photos as $page => $photo):
                $firstInRow = !($page % Yii::app()->params['Gallery']['photos_per_line']);
                ?>
                <li class="span2 <?php echo $firstInRow ? 'first-in-row' : ''; ?>">
                    <?php
                    $requestParams = array(
                        $this->getModule()->rootRoute,
                        'action' => 'ViewGalleryItem',
                        'photo_id' => $photo->id,
                        'page' => ++$page,
                    );

                    if (isset($album)) {
                        $requestParams['album'] = $album->id;
                    }

                    if (isset($without_album) && $without_album) {
                        $requestParams['without_album'] = true;
                    }
                    
                    echo CHtml::link(
                        CHtml::tag('img', array(
                            'src' => $photo->getPreviewUrl()
                        )), 
                        $requestParams, 
                        array(
                            'class' => 'thumbnail'
                        )
                    );
                    ?>
                </li>
                <?php endforeach; ?>
        </ul>
        <?php else: 
            echo Yii::t('album.messages', 'Видеозаписи отсутствуют');
        endif; ?>

    <br clear="all">

<?php if ($nphotos > $photos_page * $photos_per_page): ?>
    <div align="center">
    <?php
    $requestParams = array(
        $this->getModule()->rootRoute,
        'acion' => 'ViewAll',
        'photos_page' => ++$photos_page,
        'view' => 'photos',
        'target_id' => $target_id,
    );

    if (isset($album)) {
        $requestParams['album_id'] = $album->id;
        $requestParams['action'] = 'ViewAlbum';
    }

    if (isset($without_album) && $without_album) {
        $requestParams['without_album'] = true;
    }
    
    echo CHtml::ajaxlink(
        'Показать больше записей', 
        $requestParams, 
        array('replace' => '#replace_photos_container'), 
        array('live' => false, 'id' => 'send-link-' . uniqid())
    );
    ?>
    </div>
<?php endif; ?>

</div>