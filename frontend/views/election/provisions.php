<?php
$this->breadcrumbs->add('Provisions', '/election/provisions/' . $model->id);
$this->breadcrumbs->add($model->name, '/election/view/' . $model->id);
?>

<div id="election-info" class="row-fluid grouped-model-info">
    <div class="span12">

        <h5 data-toggle="#mandate"><?= Yii::t('election.provisions', 'Mandate'); ?>
            <?php if($canManage): ?>
            &nbsp;<small><a href="<?= Yii::app()->createUrl('/election/management/', array('id'=> $this->election->id, '#'=>'mandate')); ?>"><?= Yii::t('election.provisions', 'Change'); ?></a></small>
            <?php endif; ?>
        </h5>

        <?php $this->widget('bootstrap.widgets.TbDetailView', array(
            'htmlOptions' => array(
                'id' => 'mandate'
            ),
            'type'  => 'condensed',
            'data'  => $model,
            'attributes'=>array(
                'mandate', 'quote', 'validity'
            )
        )); ?>

        <h5 data-toggle="#candidate-registration-options"><?= Yii::t('election.provisions', 'Candidate registraion options'); ?>
            <?php if($canManage): ?>
            &nbsp;<small><a href="<?= Yii::app()->createUrl('/election/management/', array('id'=> $this->election->id, '#'=>'candidate-registration-options')); ?>"><?= Yii::t('election.provisions', 'Change'); ?></a></small>
            <?php endif; ?>
        </h5>

        <?php $this->widget('bootstrap.widgets.TbDetailView', array(
            'htmlOptions' => array(
                'id' => 'candidate-registration-options'
            ),
            'type'  => 'condensed',
            'data'  => $model,
            'attributes'=>array(
                array(
                    'name'  =>  'cand_reg_type',
                    'value' =>  AESHelper::arrTranslatedValue(Election::$cand_reg_types, $model->cand_reg_type, 'election')
                ), 
                array(
                    'name'  =>  'cand_reg_confirm',
                    'value' =>  AESHelper::arrTranslatedValue(Election::$cand_reg_confirms, $model->cand_reg_confirm, 'election')
                )
            )
        )); ?>

        <h5 data-toggle="#electorate-registration-options"><?= Yii::t('election.provisions', 'Electorate registraion options'); ?>
            <?php if($canManage): ?>
            &nbsp;<small><a href="<?= Yii::app()->createUrl('/election/management/', array('id'=> $this->election->id, '#'=>'electorate-registration-options')); ?>"><?= Yii::t('election.provisions', 'Change'); ?></a></small>
            <?php endif; ?>
        </h5>

        <?php $this->widget('bootstrap.widgets.TbDetailView', array(
            'htmlOptions' => array(
                'id' => 'electorate-registration-options'
            ),
            'type'  => 'condensed',
            'data'  => $model,
            'attributes'=>array(
                array(
                    'name' => 'voter_group_restriction',
                    'value'=> AESHelper::arrTranslatedValue(Election::$voter_group_restrictions, $model->voter_group_restriction, 'election')
                ),
                array(
                    'name'  =>  'voter_reg_type',
                    'value' =>  AESHelper::arrTranslatedValue(Election::$voter_reg_types, $model->voter_reg_type, 'election')
                ),
                array(
                    'name'  =>  'voter_reg_confirm',
                    'value' =>  AESHelper::arrTranslatedValue(Election::$voter_reg_confirms, $model->voter_reg_confirm, 'election')
                )
            )
        )); ?>                

        <h5 data-toggle="#revote-options"><?= Yii::t('election.provisions', 'Revote options'); ?>
            <?php if($canManage): ?>
            &nbsp;<small><a href="<?= Yii::app()->createUrl('/election/management/', array('id'=> $this->election->id, '#'=>'revote-options')); ?>"><?= Yii::t('election.provisions', 'Change'); ?></a></small>
            <?php endif; ?>
        </h5>
        
        <?php $this->widget('bootstrap.widgets.TbDetailView', array(
            'htmlOptions' => array(
                'id' => 'revote-options'
            ),
            'type'  => 'condensed',
            'data'  => $model,
            'attributes'=>array(
                'revotes_count', 'remove_vote_time', 'revote_time'
            )
        )); ?>        
        
    </div>
</div>

<?php
$this->widget('CommentsMarionetteWidget', array(
    'jsConstructorOptions' => array(
        'targetId' => $model->id,
        'targetType' => 'Election',
    ),                        
    'roleCheckParams' => array('election' => $model),
    'show' => array('el' => '#comments-container')
));
?>

<hr>

<div class="row-fluid">
    <div class="span12" id="comments-container"></div>
</div>