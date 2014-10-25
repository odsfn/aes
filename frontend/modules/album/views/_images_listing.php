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
                        $this->getModule()->imageRoute . '/op/view',
                        'photo_id' => $photo->id,
                        'profile' => $profile->user_id,
                        'page' => ++$page,
                    );

                    if (isset($album)) {
                        $requestParams['album'] = $album->id;
                    }

                    echo CHtml::link(
                        CHtml::tag('img', array(
                            'src' => (
                                $photo->path ? $this->getModule()->getComponent('image')->createAbsoluteUrl('160x100', $photo->path) : $this->getModule()->getAssetsUrl('img/no_album.png')
                            )
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
        <?php else: ?>
        Фотографии отсутствуют
    <?php endif; ?>

    <br clear="all">

<?php if ($nphotos > $photos_page * $photos_per_page): ?>
    <div align="center">
    <?php
    $requestParams = array(
        $this->getModule()->albumRoute,
        'photos_page' => ++$photos_page,
        'view' => 'photos',
        'profile' => $profile->user_id,
    );

    if (isset($album)) {
        $requestParams['album_id'] = $album->id;
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