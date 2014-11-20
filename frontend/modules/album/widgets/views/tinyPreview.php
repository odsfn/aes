<ul class="nav nav-pills nav-stacked album-tiny-preview">
    <li class="<?php if ($this->isModuleSelected) echo 'active'; ?>">
        <a href="<?= $this->owner->createUrl('/userPage/photos', array('id'=>$this->owner->profile->user_id)); ?>">
            <?= Yii::t('userPage', ($this->owner->self) ? 'My photos' : 'Photos'); ?>
        </a>
    </li>
    <?php if (!$this->isModuleSelected): ?>
    <li class="photos-container">
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