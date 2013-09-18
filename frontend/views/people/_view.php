<div class="row-fluid">
    <div class="user-info span9">
        
        <div class="pull-left">
            <?php $this->widget('application.widgets.UsersPhoto', array(
                'user' => $data,
                'imgWidth' => 96,
                'imgHeight'=> 96,
                'containerWidth' => '96px',
                'containerHeight' => '96px'
            )); ?>
        </div>
        
        <div class="body">
            <a href="<?= $data->pageUrl ?>"><?= $data->username ?></a> <br>
            
            <b><?php echo CHtml::encode($data->getAttributeLabel('birth_day')); ?>:</b>
                <?php echo CHtml::encode(Yii::app()->dateFormatter->formatDateTime($data->birth_day, 'medium', null)); ?>
            <br>
            
            <b><?php echo CHtml::encode($data->getAttributeLabel('birth_place')); ?>:</b>
                <?php echo CHtml::encode($data->birth_place); ?>
            <br>
        </div>
        
    </div>
</div>
