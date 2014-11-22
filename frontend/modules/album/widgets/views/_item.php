<li class="span12">
    <div class="thumbnail">
        <div>
            <?php if ($preview->captionHasContent): ?>
            <div class="caption-transparent caption-bottom">
                <?php if ($preview->title): ?>
                <h5>
                    <span class="head-text" title="<?= Yii::t('album.messages', 'Наименование альбома'); ?>"><a href="<?php echo $preview->itemUrl; ?>"><?php echo $preview->title; ?></a></span>
                    <?php if ($preview->update): ?>
                    <small class="pull-right">
                        <i class="icon-time" title="<?= Yii::t('album.messages', 'Дата обновелния альбома'); ?>"></i>
                        <?php echo Yii::app()->locale->dateFormatter->formatDateTime($preview->update, 'short', 'short'); ?>
                    </small>
                    <?php endif; ?>
                </h5>
                <?php endif; ?>
                <?php if ($preview->description): ?>
                <p class="caption-hidable"><?php echo $preview->description; ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <a href="<?php echo $preview->imageUrl; ?>">
                <img src="<?php echo $preview->imageSrc; ?>">
            </a>                        
        </div>
    </div>
</li>