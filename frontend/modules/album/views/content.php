<?php
Yii::app()->clientScript->registerCssFile($this->getModule()->getAssetsUrl('css/gallery.css'));
?>
<div class="photo-gallery">

    <?php
    $this->widget('bootstrap.widgets.TbMenu', array(
        'type' => 'tabs', // '', 'tabs', 'pills' (or 'list')
        'stacked' => false, // whether this is a stacked menu
        'items' => $menu,
    ));
    ?>

    <div align="center">
    <?php echo $content; ?>
    </div>
</div>