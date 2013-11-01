<?php 
$this->layout = '//layouts/main';
$election = Election::model()->findByPk(1);
?>

<h1>RatesWidget sandbox</h1>

<hr>

<i>With fetching set of models</i>

<div id="something-1">
<p>Lorem ipsum dolor. And comments to it below.</p>

<?php 
    $this->widget('RatesMarionetteWidget', array(
        'jsConstructorOptions' => array(
            'targetId' => $election->id,
            'targetType' => 'Election',
            'targetEl' => '#something-1',
        ),
        'show' => array('div')
    ));
?>
</div>