<?php
Yii::app()->clientScript->registerCssFile($this->getModule()->getAssetsUrl('css/gallery.css'));
?>
<div class="row-fluid">
    <div class="photo-gallery span12">

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