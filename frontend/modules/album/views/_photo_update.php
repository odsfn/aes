<div class="row-fluid">
<?php
/* @var $this AlbumController */
/* @var $model Album */
/* @var $form CActiveForm */
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'photo-form',
    'action' => !empty($action) ? $action : $this->createAbsoluteUrl(
        $this->getModule()->ajaxUpdateImageRoute, 
        array(
            'photo_id' => $model->id, 
            'albumContext' => !empty($albumContext) ? $albumContext : false
        )
    ),
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'class'=>'span12',
    )
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->labelEx($model,'description'); ?>
    <?php echo $form->textArea($model,'description', array('class' => 'span12')); ?>
    <?php echo $form->error($model,'description'); ?>
        
    <?php echo $form->labelEx($model,'album_id'); ?>
    <?php
        echo $form->dropDownList($model,'album_id',
            AlbumModule::albumsAsListData(!empty($model->target_id) ? $model->target_id : $target_id),
            array('class' => 'span12')
        );
    ?>
    <?php echo $form->error($model,'album_id'); ?>    
    
    <?php echo $form->labelEx($model,'permission'); ?>
    <?php
        echo $form->dropDownList($model,'permission',
            array(
                'Всем',
                'Только зарегистрированным пользователям',
                'Только мне'
            ),
            array(
                'class' => 'span12',
                'disabled' => !empty($model->album) ? 'disabled' : false
            )
        );
    ?>
    <?php echo $form->error($model,'permission'); ?>
    <p class="muted"><small class="hint-permission">
        <?= Yii::t('album', 'Фотография будет иметь такой же уровень доступа как и у альбома, в котый она перемещается.'); ?>
    </small></p>
    <?php 
        Yii::app()->clientScript->registerScript('albumSwitchHandler', 
            "var albumSwitchHandler = function() {"
                . "var albumId = $('select#File_album_id').val();"
                . "if(albumId !=='NULL' && albumId != '') {"
                    . "$('select#File_permission').prop('disabled', 'disabled');"
                    . "$('small.hint-permission').show();"
                . "} else {"
                    . "$('select#File_permission').prop('disabled', false);"
                    . "$('small.hint-permission').hide();"
                . "}"
            . "};"
            . "albumSwitchHandler();"
            . "$('body').on('change', 'select#File_album_id', function(e) {"
                . "albumSwitchHandler();"
            . "})");
    ?>
    
    <div class="row-fluid buttons">
        <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Сохранить')); ?>
        <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'reset', 'label'=>'Отменить')); ?>
    </div>
<?php $this->endWidget(); ?>

</div>