<h3><span class="text-error">
<?php
echo Yii::t('aes','Error'); 

if(isset($type) && $type == 'CHttpException' 
    && isset($code)
) {
    echo '&nbsp;' . $code;
}
?>
</span></h3>
<p class="text-error"><?php echo CHtml::encode( $message ); ?></p>

<?php if($type == 'CHttpException' && YII_DEBUG): ?>
<p><b>Debug info:</b></p>
<p class="text">
<?php
echo CHtml::encode($file) . ':';
echo CHtml::encode($line) . '<br>';
?>
</p>

Trace:
<pre>
<?= $trace; ?>
</pre>

Source:
<pre>
<?= $source; ?>
</pre>
<?php endif; ?>
