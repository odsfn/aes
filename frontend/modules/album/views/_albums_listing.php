<script type="text/javascript">
$(function() {
    $('.thumbnails.gallery > li').hover(
            function() {
                $(this).find('.caption-hidable').slideDown(250);
            },
            function() {
                $(this).find('.caption-hidable').slideUp(250);
            }
    );
});
</script>
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
                            <?php if($album->description): ?>
                            <div class="caption-transparent caption-bottom caption-hidable">
                                <p><?= $album->description ?></p>
                            </div>
                            <?php endif; ?>
                            <?php
                            echo CHtml::link(
                                    CHtml::tag('img', array(
                                        'src' => ( 
                                            $album->path ? $this->getModule()->getComponent('image')->createAbsoluteUrl('360x220', $album->path) : $this->getModule()->getAssetsUrl('img/no_album.png'))
                                    )), 
                                    array(
                                        $this->getModule()->albumRoute . '/op/view',
                                        'album_id' => $album->id,
                                        'target_id' => $target_id,
                                    )
                                );
                            ?>
                        </div>
                        <div class="caption">
                            <h5>
                            <span class="head-text"><?php 
                            echo CHtml::link($album->name, array($this->getModule()->albumRoute . '/op/view', 'album_id' => $album->id)); 
                            ?></span>
                                <small class="pull-right" title="<?= Yii::t('album', 'Дата обновелния альбома'); ?>"><i class="icon-time"></i>&nbsp;<?php echo Yii::app()->locale->dateFormatter->formatDateTime($album->update, 'short', 'short'); ?></small>
                            </h5>
                        </div>
                    </div>
                </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        Фотоальбомы отсутствуют
<?php endif; ?>

    <br clear="all">
        <?php if ($nalbums > $albums_page * $albums_per_page): ?>
        <div align="center"><?php
            echo CHtml::ajaxlink('Показать больше записей', array(
                $this->getModule()->albumRoute,
                'albums_page' => ++$albums_page,
                'view' => 'albums',
                'target_id' => $target_id,
                    ), array('replace' => '#replace_albums_container'), array('live' => false, 'id' => 'send-link-' . uniqid()));
            ?>
        </div>
<?php endif; ?>
</div>