<?php
$this->pageTitle = 'People | AES';

$this->layout = 'column2';

Yii::app()->clientScript->registerScript('search', "    
    $('.search-form form').submit(function(){
        $.fn.yiiGridView.update('profile-grid', {
            data: $(this).serialize()
        });
        
        return false;
    });
    
    $('#PeopleSearch_birth_day').change(function(){
        $('#PeopleSearch_ageFrom, #PeopleSearch_ageTo').val('');
    });
    
    $('#PeopleSearch_ageFrom, #PeopleSearch_ageTo').change(function(){
        $('#PeopleSearch_birth_day').val('');
    });
    
    $(\"input[name='reset']\").click(function() {
        $('.search-form form').children('input, select').val('');
        $('.search-form form button[type=\"submit\"]').click();
    });
");

$this->beginCLip('sidebar'); ?>

    <div class="search-form">
    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
        'htmlOptions'=>array('class'=>'well'),
        'enableClientValidation' => true
    )); ?>

            <?php echo $form->textFieldRow($model, 'name', array('class'=>'span12', 'maxlength'=>128)); ?>

            <?php echo $form->datepickerRow($model, 'birth_day',
                array('prepend'=>'<i class="icon-calendar"></i>','class'=>'span11')); ?>        
        
            <?php echo $form->textFieldRow($model,'birth_place',array('class'=>'span12','maxlength'=>128)); ?>

            <?php echo $form->textFieldRow($model,'ageFrom',array('class'=>'span12')); ?>
        
            <?php echo $form->textFieldRow($model,'ageTo',array('class'=>'span12')); ?>

            <?php echo $form->dropDownListRow($model, 'gender',
                array('' => 'Any', '1' => 'Male', '2' => 'Famale'), array('class'=>'span12')); 
            ?>

            <div class="form-actions">
                <?php $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'submit',
                    'type'=>'primary',
                    'label'=>'Search',
                )); ?>

                <input type="button" class="btn" name="reset" value="Reset">
            </div>    
    <?php $this->endWidget(); ?>
    </div><!-- search-form -->
    
<?php $this->endClip();?>

<?php $this->widget('bootstrap.widgets.TbListView',array(
    'summaryText' => Yii::t('people', 'Found {count} persones'),
    'dataProvider'=>$model->search(),
    'itemView'=>'_view',
)); ?>
