<?php
/* @var $this AlbumController */
/* @var $model Album */
/* @var $form CActiveForm */

$form=$this->beginWidget('CActiveForm', array(
    'id'=>'album-album-form',
    'enableAjaxValidation'=>false,
)); ?>

    <div class="row">
      <?php echo CHtml::tag('img', array('src'=> ($model->path? $this->getModule()->getComponent('image')->createAbsoluteUrl('100x100', $model->path): $this->getModule()->getAssetsUrl('img/no_album.png')) )); ?>
    </div>

    <?php if($model->id):?>
      <?php echo CHtml::link('Удалить', array($this->getModule()->albumRoute . '/op/delete', 'album_id' => $model->id)); ?>
    <?php endif;?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name'); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textArea($model,'description'); ?>
        <?php echo $form->error($model,'description'); ?>
    </div>

  <div class="row">
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
  </div>

  <div class="row buttons">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>'Сохранить')); ?>
  </div>

<?php $this->endWidget(); ?>