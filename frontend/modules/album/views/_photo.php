<?php if ($this->getModule()->ajaxImageNavigation) : ?>
<script type="text/javascript">
$(function() {
    //make ajax navigation
    $('#image-view-container').on('click', '#image-view .pagination a', function(event){
        event.preventDefault();
        var el = $(this);
        if (el.parent().hasClass('disabled'))
            return;
        
        $('#image-view .pagination li').addClass('disabled');
        $('#image-view .pagination').append('<p>Loading...</p>');
        $('#image-view-container').load(el.attr('href') + ' #image-view');
    });
});
</script>
<?php endif; ?>
<div id="image-view-container">
    <div id="image-view" class="row-fluid">
        <ul class="thumbnails">
            <li class="span12">
                <div class="thumbnail without-border">
                <?php 
                echo CHtml::tag('img', 
                    array('src'=> 
                        ($model->path ? $this->getModule()->getComponent('image')->createAbsoluteUrl('1150x710', $model->path) : $this->getModule()->getAssetsUrl('img/no_album.png'))
                    )
                ); 
                ?>
                </div>
            </li>
        </ul>
        <hr>
        <div class="row-fluid">
            <div class="span10">
                <div class="row-fluid">
                    <div class="span10">
                        <p class="description"><?php echo $model->description; ?></p>
                        <p class="muted">
                            <b>Загружено:</b> <?php echo Yii::app()->locale->dateFormatter->formatDateTime($model->update, 'medium', 'short'); ?>
                            <br>
                            <b>Альбом:</b>
                            <?php echo (isset($model->album)? CHtml::link($model->album->name, array(
                              $this->getModule()->albumRoute . '/op/view',
                              'album_id' => $model->album->id
                             )): '-')?>
                        </p>
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
                                echo CHtml::link(
                                    'Назначить обложкой', 
                                    array($this->getModule()->imageRoute . '/op/album', 'photo_id' => $model->id),
                                    array('class' => 'btn btn-mini btn-block')
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
            </div>

            <div class="span2 text-right">
                <div class="pagination">
                <?php $this->widget('bootstrap.widgets.TbPager', array(
                    'pages' => $pages,
                    'maxButtonCount' => 0,
                    'alignment' => 'right',
                    'header' => ''
                ))?>
                    <p><?php echo ($pages->currentPage + 1) . ' из '. $pages->itemCount; ?></p>
                </div>
            </div>

        </div>
    </div>
</div>
