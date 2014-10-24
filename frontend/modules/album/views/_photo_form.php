<?php
$this->widget('CLinkPager', array(
    'pages' => $pages,
    'maxButtonCount' => 0,
    'header' => '',
))
?> <?php echo ($pages->currentPage + 1); ?> из <?php echo $pages->itemCount; ?>

<div class="row">
<?php echo CHtml::tag('img', array('src' => ($model->path ? $this->getModule()->getComponent('image')->createAbsoluteUrl('600x480', $model->path) : $this->getModule()->getAssetsUrl('img/no_album.png')))); ?>
</div>

<div align="left">

    <div class="row-fluid">
        <div class="span7" style="text-align:left;">
            <p>Загружено <strong><?php echo $model->update ?></strong></p>
            <p>
                <strong>Описание:</strong>&nbsp;
                <span id="description" title="Изменить" style="">-Изменить-</span><br>
                <strong>Альбом:</strong>
                <?php
                echo (isset($model->album) ? CHtml::link($model->album->name, array(
                            $this->getModule()->albumRoute . '/op/view',
                            'album_id' => $model->album->id
                        )) : '-Нет-')
                ?>
                <br>
            </p>
        </div>

        <div class="span5" style="text-align:right;">
            <p></p>
            <p>
                <?php if (isset($model->album)): ?>
                    <?php echo CHtml::link('Назначить обложкой альбома', array($this->getModule()->imageRoute . '/op/album', 'photo_id' => $model->id)); ?><br>
<?php endif; ?>
                <br>
<?php echo CHtml::link('Скачать', $this->getModule()->getComponent('image')->createAbsoluteUrl('original', $model->path)); ?><br>
<?php echo CHtml::link('Удалить', array($this->getModule()->imageRoute . '/op/delete', 'photo_id' => $model->id)); ?>
            </p>
        </div>
    </div>

</div>