<div class="row-fluid">
<?php
/* @var $this AlbumController */
/* @var $model Album */
/* @var $form CActiveForm */
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'album-album-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'class'=>'span12',
    )
)); ?>

    <div class="span4">
    <?php echo CHtml::tag('img', array(
        'src'=> $model->getCoverUrl(),
        'class' => 'img-polaroid span12'
    )); ?>
    </div>
    
    <div class="span8">
        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>


        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name'); ?>
        <?php echo $form->error($model,'name'); ?>



        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textArea($model,'description'); ?>
        <?php echo $form->error($model,'description'); ?>

        <?php echo $form->labelEx($model,'permission'); ?>
        <?php
            echo $form->dropDownList($model,'permission',
                array(
                    'Всем',
                    'Только зарегистрированным пользователям',
                    'Только мне'
                )
            );
        ?>
        <?php echo $form->error($model,'permission'); ?>

        <div class="row-fluid buttons">
            <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Сохранить')); ?>
            <?php if($model->id):?>
                <?php echo CHtml::link(
                    'Удалить', array($this->getModule()->albumRoute . '/op/delete', 'album_id' => $model->id),
                    array('class' => 'btn btn-danger')
                ); ?>
            <?php endif;?>
        </div>
    </div>
<?php $this->endWidget(); ?>
</div>