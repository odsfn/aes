<div id="replace_gitems_container">
    <?php if ($gitems): ?>
        <ul class="thumbnails gallery">
            <?php
            foreach ($gitems as $page => $gitem):
                $firstInRow = !($page % Yii::app()->params['Gallery']['gitems_per_line']);
                ?>
                <li class="span2 <?php echo $firstInRow ? 'first-in-row' : ''; ?>">
                    <?php
                    $requestParams = array(
                        $this->getModule()->rootRoute,
                        'action' => 'ViewGalleryItem',
                        'gitem_id' => $gitem->id,
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
                            'src' => $gitem->getPreviewUrl()
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
            echo Yii::t('album.messages', $this->getAction()->pluralLabel . ' отсутствуют');
        endif; ?>

    <br clear="all">

<?php if ($ngitems > $gitems_page * $gitems_per_page): ?>
    <div align="center">
    <?php
    $requestParams = array(
        $this->getModule()->rootRoute,
        'acion' => 'ViewAll',
        'gitems_page' => ++$gitems_page,
        'view' => 'gitems',
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
        array('replace' => '#replace_gitems_container'), 
        array('live' => false, 'id' => 'send-link-' . uniqid())
    );
    ?>
    </div>
<?php endif; ?>

</div>