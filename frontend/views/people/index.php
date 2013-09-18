<?php
$this->layout = 'column2';

Yii::app()->clientScript->registerScript('search', "    
    $('.search-form form').submit(function(){
        $.fn.yiiGridView.update('profile-grid', {
            data: $(this).serialize()
        });
        
        return false;
    });
");

$this->beginCLip('sidebar'); ?>

    <div class="search-form">
    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
        'htmlOptions'=>array('class'=>'well'), 
    )); ?>

            <?php echo $form->textFieldRow($model, 'name', array('class'=>'span12', 'maxlength'=>128)); ?>

            <?php echo $form->textFieldRow($model,'birth_place',array('class'=>'span12','maxlength'=>128)); ?>

            <?php echo $form->textFieldRow($model,'ageFrom',array('class'=>'span12')); ?>
        
            <?php echo $form->textFieldRow($model,'ageTo',array('class'=>'span12')); ?>

            <?php echo $form->dropDownListRow($model, 'gender',
                array('' => 'Any', '1' => 'Male', '2' => 'Famale'), array('class'=>'span12')); 
            ?>

            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                'type'=>'primary',
                'size' => 'large',
                'label'=>'Search',
            )); ?>

    <?php $this->endWidget(); ?>
    </div><!-- search-form -->
    
<?php $this->endClip();?>

<?php $this->widget('bootstrap.widgets.TbListView',array(
    'summaryText' => Yii::t('people', 'Found {count} persones'),
    'dataProvider'=>$model->search(),
    'itemView'=>'_view',
)); ?>
