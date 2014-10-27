<?php $mid = 'message_' . uniqid() ?>
<p id="<?= $mid ?>"><span class="label label-success"><?= Yii::t('album', 'Назначено обложкой'); ?></span></p>
<script type="text/javascript">
$('#<?= $mid ?>').delay('750').fadeOut();
</script>