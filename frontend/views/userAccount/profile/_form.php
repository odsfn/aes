<div class="row-fluid">

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
            'id'=>'RegistrationForm',
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>true,
            'type'=>'vertical',
            'htmlOptions'=>array(
                'class'=>'well span9 offset1',
                'enctype' => 'multipart/form-data',
            )
    )); ?>

        <div class="row-fluid">
            <div class="span12">
                <h3>Registration</h3>

                <p class="help-block">Fields with <span class="required">*</span> are required.</p>
                <p></p>

                <?php echo $form->errorSummary(array($model, $personIdent)); ?>
            </div>
        </div>
    
        <div class="row-fluid">
            
            <div class="span6">
                <h5>Profile information</h5>
                <?php 
                    echo $form->passwordFieldRow($model, 'password', array('class'=>'span12', 'maxlength'=>128));

                    echo $form->passwordFieldRow($model, 'password_check', array('class'=>'span12', 'maxlength'=>128));
                ?>

                <?php echo $form->textFieldRow($model,'email',array('class'=>'span12', 'maxlength'=>128)); ?>

                <?php echo $form->maskedTextFieldRow($model,'mobile_phone', '+9(999)999-99-99', array('class'=>'span12', 'maxlength'=>18)); ?>

                <?php echo $form->textFieldRow($model,'first_name',array('class'=>'span12','maxlength'=>128)); ?>

                <?php echo $form->textFieldRow($model,'last_name',array('class'=>'span12','maxlength'=>128)); ?>

                <?php echo $form->textFieldRow($model,'birth_place',array('class'=>'span12','maxlength'=>128)); ?>

                <?php echo $form->datepickerRow($model, 'birthDayFormated',
                    array('prepend'=>'<i class="icon-calendar"></i>','class'=>'span9')); ?>

                <?php echo $form->dropDownListRow($model, 'gender',
                    array('', '1' => 'Male', '2' => 'Famale'), array('class'=>'span12')); 
                ?>
            </div>
            
            <div class="span6">
                <h5>Person Identifier</h5>
                <?php $this->widget('personIdentifier.widgets.IdentifierInput', array('identifier' => $personIdent, 'form' => $form)); ?>
            </div>
            
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="form-actions">
                        <?php $this->widget('bootstrap.widgets.TbButton', array(
                                        'buttonType'=>'submit',
                                        'type'=>'primary',
                                        'size'=>'large',
                                        'label'=>'Create Account',
                                )); ?>
                </div>
            </div>
        </div>

    <?php $this->endWidget(); ?>

</div>
