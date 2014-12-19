<?php
Yii::app()->clientScript
    ->registerScriptFile($this->getModule()->getAssetsUrl('js/captionTransparent.js'));
?>
<div id="replace_albums_container">
    <?php if ($albums): ?>
        <ul class="thumbnails gallery">
            <?php
            $albums_per_line = Yii::app()->params['Gallery']['albums_per_line'];
            foreach ($albums as $index => $album):
                $isNewRow = !($index % $albums_per_line);     //considering that each page is new line
                ?>
                <li class="span4 <?php if ($isNewRow) echo 'first-in-row'; ?>">
                    <div class="thumbnail">
                        <div>
                            <div class="caption-transparent caption-bottom">
                                <h5>
                                    <span class="head-text" title="<?= Yii::t('album.messages', 'Наименование альбома'); ?>"><?php 
                                    echo CHtml::link($album->name, array($this->getModule()->rootRoute, 'action' => 'ViewAlbum', 'album_id' => $album->id)); 
                                    ?></span>
                                    <small class="pull-right" title="<?= Yii::t('album', 'Дата обновелния альбома'); ?>"><i class="icon-time"></i>
                                        <?php echo Yii::app()->locale->dateFormatter->formatDateTime($album->update, 'short', 'short'); ?></small>
                                </h5>
                                <?php if($album->description): ?>
                                <p class="caption-hidable"><?= $album->description ?></p>
                                <?php endif; ?>
                            </div>
                            <?php
                            
                            if ($album->cover)
                                $imageUrl = array(
                                    $this->getModule()->rootRoute,
                                    'action' => 'ViewGalleryItem',
                                    'gitem_id' => $album->cover->id,
                                    'album' => $album->id,
                                    'exact' => true
                                );
                            else
                                $imageUrl = array(
                                    $this->getModule()->rootRoute,
                                    'action' => 'ViewAlbum',
                                    'album_id' => $album->id
                                );
                            
                            echo CHtml::link(
                                    CHtml::tag('img', array(
                                        'src' => $album->getCoverUrl()
                                    )), 
                                    $imageUrl
                                );
                            ?>
                        </div>
                    </div>
                </li>
        <?php endforeach; ?>
        </ul>
    <?php else:
        echo Yii::t('album.messages', 'Альбомы отсутствуют');
    endif; ?>

    <br clear="all">
        <?php if ($nalbums > $albums_page * $albums_per_page): ?>
        <div align="center"><?php
            echo CHtml::ajaxlink('Показать больше записей', array(
                $this->getModule()->rootRoute,
                'albums_page' => ++$albums_page,
                'view' => 'albums',
                'target_id' => $target_id,
                    ), array('replace' => '#replace_albums_container'), array('live' => false, 'id' => 'send-link-' . uniqid()));
            ?>
        </div>
<?php endif; ?>
</div>