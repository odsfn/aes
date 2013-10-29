<?php 
$this->layout = '//layouts/main';
$election = Election::model()->findByPk(1);
?>

<h1>Hello from sandbox!</h1>

<hr>

<i>With fetching set of models</i>

<p>Lorem ipsum dolor. And comments to it below.</p>

<div class="row-fluid">
    <?php

    $this->widget('CommentsMarionetteWidget', array(
        'jsConstructorOptions' => array(
            'targetId' => $election->id,
            'targetType' => 'Election',
            'title' => true,
            'limit' => 2
        ),
        'show' => array('div', array('class'=>'span4'))
    ));

    ?>    
</div>

<hr>

<i>Empty comments</i>

<p>Lorem ipsum dolor. And comments to it below.</p>

<div class="row-fluid">
    <div id="comments-box" class="span4"></div>
</div>

<?php

$this->widget('CommentsMarionetteWidget', array(
    'jsConstructorOptions' => array(
        'targetId' => 3,    //$election->id here
        'targetType' => 'Election',
        'autoFetch' => 0
    ),
    'show' => array('el' => '#comments-box'),
    'roleCheckParams' => array('election' => $election)
));

?>

<hr>

<i>With initial set of models</i>

<p>Lorem ipsum dolor. And comments to it below.</p>

<div class="row-fluid">
    <div id="comments-box-with-data-set" class="span4"></div>
</div>

<?php 

Yii::app()->clientScript->registerScript('initCommentsWidgetWithDataSet', "
var widgetWithDataSet = CommentsWidget.create({

    targetId: 2,
    
    targetType: 'Election',
    
    initData: {
    
        totalCount: 2,
        models: [
        
                {
                    id: _.uniqueId(),
                    user_id: 2,
                    user: {
                        displayName: 'Another User',
                        photo: \"http://placehold.it/64x64\"
                    },
                    content: \"Lorem ipsum dolor sit amet, at debet dolores est.\",
                    created_ts: 1376577788,                
                },
                
                {
                    id: _.uniqueId(),
                    user_id: 1,
                    user: {
                        displayName: 'Vasiliy Pedak',
                        photo: \"http://placehold.it/64x64\"
                    },
                    content: \"At debet dolores est. Lorem ipsum dolor sit amet\",
                    created_ts: 1376577889,                   
                }        
        ]
    }
});

$('#comments-box-with-data-set').html(widgetWithDataSet.render().el);
widgetWithDataSet.triggerMethod('show');
", CClientScript::POS_READY);

?>