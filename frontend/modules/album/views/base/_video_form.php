<script type="text/javascript">
    $(function(){
        $('#gitem-form').submit(function(e){
            $('#gitem-form button[type="submit"]').attr('disabled', 'disabled');
            $('body').css('cursor', 'wait');
        });
    });
</script>
<div class="row-fluid">
<?php
/* @var $this AlbumController */
/* @var $model Album */
/* @var $form CActiveForm */
$formOptions = array(
    'id'=>'gitem-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'class'=>'span12',
    )
);

if(!empty($action))
    $formOptions['action'] = $action;

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', $formOptions); ?>
    
    <div class="span12">
        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>

        
        <?php
        if($model->isNewRecord) {
            echo $form->labelEx($model,'url');
            echo $form->textField($model,'url', array('class' => 'span12'));
            echo $form->error($model,'url'); 
        }
        ?>

        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textArea($model,'description', array('class' => 'span12')); ?>
        <?php echo $form->error($model,'description'); ?>

        <?php echo $form->labelEx($model,'album_id'); ?>
        <?php
            echo $form->dropDownList($model,'album_id',
                AlbumModule::albumsAsListData(
                    !empty($model->target_id) ? $model->target_id : $target_id, 
                    ($model instanceof Video) ? 'VideoAlbum' : 'Album'
                ),
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
                    'disabled' => (!$model->isNewRecord && !empty($model->album)) ? 'disabled' : false
                )
            );
        ?>
        <?php echo $form->error($model,'permission'); ?>
            <p class="muted"><small class="hint-permission">
                <?= Yii::t('album', 'Видеозапись будет иметь такой же уровень доступа как и у альбома, в котый она перемещается.'); ?>
            </small></p>
            <?php 
            Yii::app()->clientScript->registerScript(
                'albumSwitchHandler', 
                "var itemType = '{$this->getAction()->albumItemType}';"
                . "var albumSwitchHandler = function() {"
                    . "var albumId = $('select#'+itemType+'_album_id').val();"
                    . "if(albumId !=='NULL' && albumId != '') {"
                        . "$('select#'+itemType+'_permission').prop('disabled', 'disabled');"
                        . "$('small.hint-permission').show();"
                    . "} else {"
                        . "$('select#'+itemType+'_permission').prop('disabled', false);"
                        . "$('small.hint-permission').hide();"
                    . "}"
                . "};"
                . "albumSwitchHandler();"
                . "$('body').on('change', 'select#'+itemType+'_album_id', function(e) {"
                    . "albumSwitchHandler();"
                . "})"
            );
            ?>

        <div class="row-fluid buttons">
            <?php 
            $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Сохранить'));
            echo '&nbsp;';
            if($showCancelBtn) { 
                $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'reset', 'label'=>'Отменить'));
            } elseif($model->id) {
                echo CHtml::link(
                    'Удалить', array($this->getModule()->rootRoute, 'action' => 'DeleteGalleryItem', 'gitem_id' => $model->id),
                    array('class' => 'btn btn-danger')
                );
            } ?>
        </div>
    </div>
<?php $this->endWidget(); ?>
</div>