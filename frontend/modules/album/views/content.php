<?php
Yii::app()->clientScript
    ->registerCssFile($this->getModule()->getAssetsUrl('css/gallery.css'))
    ->registerCssFile($this->getModule()->getAssetsUrl('css/thumbnails.css'));
?>
<div class="row-fluid">
    <div class="gitem-gallery span12">

        <?php
        $this->widget('bootstrap.widgets.TbMenu', array(
            'type' => 'pills', // '', 'tabs', 'pills' (or 'list')
            'stacked' => false, // whether this is a stacked menu
            'items' => $menu,
        ));
        ?>

        <?php echo $content; ?>

    </div>
</div>