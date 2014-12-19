<ul class="nav nav-pills nav-stacked album-tiny-preview">
    <li class="<?php if ($this->isModuleSelected) echo 'active'; ?>">
        <a href="<?= $this->owner->createUrl($this->albumRoute); ?>">
            <?= $this->titleText; ?>
        </a>
    </li>
    <?php if (!$this->isModuleSelected && count($previews)): ?>
    <li class="gitems-container">
        <ul class="thumbnails gallery">
            <?php 
            foreach ($previews as $preview) {
                $this->render('_item', array('preview'=>$preview));
            } 
            ?>
        </ul>
    </li>
    <?php endif; ?>
</ul>