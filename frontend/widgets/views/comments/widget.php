<div class="row-fluid comments-widget-simple">
    <div class="span12" id="comments-container" class="full-ui-disabled">
        <div>
            <div class="navbar head">
                <div class="navbar-inner">
                    <ul class="nav">
                        <li><a><b>
                        <?= Yii::t('messages', 'Последние комментарии'); ?>
                        </b></a></li>
                    </ul>

                    <ul class="nav pull-right">
                        <li><a class="msgs-count">
                            <img src="/img/loader-circle-16.gif" class="loader">
                            <?= Yii::t('messages', 'Всего'); ?>: <span><?= $totalCount; ?></span>
                        </a></li>
                        <li class="load-btn-cnt">
                            <?php if($totalCount > $this->commentsToShow): ?>
                            <button 
                                class="open-comments btn more" 
                                data-target-id="<?= $targetId; ?>" 
                                data-target-type="<?= $targetType; ?>"
                            >
                                <?= Yii::t('messages' ,'Показать все'); ?>
                            </button>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
            
        <div>
            <div class="comments-feed">
            <?php 
            if($totalCount == 0) {
                echo '<p>' . Yii::t('messages' ,'Коммментариев не найдено') . '</p>';
            } else {
                foreach ($comments as $index => $comment) {
                    $this->render('_comment', array('model' => $comment));
                }
            }
            ?>
            </div>
        </div>
    </div>
    
    <p>
        <?php if($canComment): ?>
        <a 
            class="open-comments btn" 
            data-target-id="<?= $targetId; ?>" 
            data-target-type="<?= $targetType; ?>"
            title="<?= Yii::t('messages', 'Нажмите чтобы комментировать или оценинить комментарии других пользователей');?>"
        ><?= Yii::t('messages' ,'Оставить отзыв'); ?></a>
        <?php endif; ?>
    </p>
</div>
