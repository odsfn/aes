<div class="row-fluid photo-edit-panel">
<?php
/* @var $this AlbumController */
/* @var $model Album */
/* @var $form CActiveForm */
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'photo-form-' . $model->id,
    'action' => !empty($action) ? $action : $this->createAbsoluteUrl(
        $this->getModule()->ajaxUpdateImageRoute, 
        array(
            'photo_id' => $model->id,
            'form_type' => 'compact'
        )
    ),
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'class'=>'span12 well photo-edit-form',
    )
)); ?>
    <div class="row-fluid">
                    
        <div class="span3 left-col">
            <div class="row-fluid">
                <img class="span12" 
                    src="<?= $this->getModule()->getComponent('image')->createAbsoluteUrl('160x100', $model->path); ?>"
                >
            </div>

            <div class="row-fluid buttons">
                <div class="span12">
                    <?php 
                    echo CHtml::link(
                        'Повернуть против часовой стрелки', 
                        array('/album/image/rotateImage', 'photo_id' => $model->id, 'direction' => 'left'),
                        array('class' => 'rotate')
                    );
                    
                    echo CHtml::link(
                        'Повернуть по часовой стрелке', 
                        array('/album/image/rotateImage', 'photo_id' => $model->id, 'direction' => 'right'),
                        array('class' => 'rotate')
                    );
                    
                    $this->widget('bootstrap.widgets.TbButton', array(
                        'buttonType'=>'submit', 'label'=>'Сохранить изменения',
                        'htmlOptions' => array('class' => 'btn-block btn-mini')
                    )); ?>
                    <?php 
                        echo CHtml::link(
                            'Удалить', 
                            array('/album/image/photoDelete', 'photo_id' => $model->id),
                            array('class' => 'btn btn-danger btn-mini btn-block photo-delete')
                        );
                    ?>
                </div>
            </div>
            
            <div class="row-fluid">
                <div class="span12 marks">
                </div>
            </div>
        </div>
        
        <div class="span9">
            
        <?php if (isset($updated)): ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <small><?php echo Yii::t('album', 'Изменения успешно сохранены в {time}', array('{time}'=>$updated)); ?></small>
            </div>
        <?php else:
                echo $form->errorSummary($model); 
            endif;
        ?>

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
        
        </div> 
    </div>
    
<?php $this->endWidget(); ?>
</div>
