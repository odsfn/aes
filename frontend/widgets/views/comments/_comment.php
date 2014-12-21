<div class="media post">
    <div class="pull-left">
        <div class="img-wrapper-tocenter users-photo users-photo-<?= $model->user->user_id; ?>">
            <span></span>
            <a href="<?= $model->user->pageUrl; ?>" name="<?= $model->id; ?>">
                <img alt="<?= $model->user->username; ?>" src="<?= $model->user->photoThmbnl64; ?>">
            </a>
        </div>
    </div>
    <div class="media-body">
        <div class="post-body">
            <h5 class="media-heading">
                <span class="user"><?= $model->user->username; ?></span> 
                <small>
                    <a href="#<?= $model->id; ?>">
                        <?= Yii::app()->locale->dateFormatter->formatDateTime($model->created_ts, 'medium', 'medium'); ?>
                    </a>
                </small> 
            </h5>

            <div class="post-content">
                <?= $model->content ?>
            </div>

            <div class="post-after">
                <div class="post-rate pull-right">
                    <div class="rates">
                        <div class="rate-control">
                            <span class="icon-thumbs-up"><?= $model->positiveRatesCount; ?></span>
                            <span class="icon-thumbs-down"><?= $model->negativeRatesCount; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="comments"></div>
    </div>
</div>