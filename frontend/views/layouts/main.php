<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?= $this->pageTitle; ?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">

        <?php
        Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/layouts/main.css'); 
        Yii::app()->clientScript->registerScriptFile('/js/libs/jquery.blockUI.js');
        Yii::app()->clientScript->registerScriptFile('/js/libs/jquery.smartLoad.js');
        ?>
	<!--<script src="<?php echo Yii::app()->getBaseUrl(true); ?>/js/libs/modernizr-2.6.2-respond-1.1.0.min.js"></script>-->
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
	your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to
	improve your experience.</p>
<![endif]-->

<!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

                    <a class="brand" href="<?= Yii::app()->createAbsoluteUrl('/'); ?>">AES</a>

			<div class="nav-collapse collapse">
                                <?php $this->widget('bootstrap.widgets.TbMenu', array(
                                    'items'=>array(
                                        array('divider'=>'', 'visible'=>!Yii::app()->user->isGuest),
                                        array('label'=>'Your page', 'url'=>array('/userPage/index', 'id'=>Yii::app()->user->id), 'visible'=>!Yii::app()->user->isGuest, 'active' => ( $this->id == 'userPage' && $this->profile->user_id == Yii::app()->user->id)),
                                        array('divider'=>''),
                                        array('label'=>'Elections', 'url'=>array('/election/index'), 'active' => $this->id == 'election'),
                                        array('divider'=>''),
                                        array('label'=>'Mandates', 'url'=>array('/mandate/index'), 'active' => $this->id == 'mandate'),
                                        array('divider'=>''),                                        
                                        array('label'=>'People', 'url'=>array('/people/index'), 'active' => ( $this->id == 'people' || ( $this->id == 'userPage' && $this->profile->user_id != Yii::app()->user->id ))),
                                        array('divider'=>''),
                                        array('label'=>'About', 'url'=>'#'),
                                    ),
                                )); ?>
				
				<?php if(!Yii::app()->user->isGuest) :?>
				<ul class="nav pull-right">
				    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= Yii::app()->user->username; ?>&nbsp;<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li class="nav-header">Settings</li>
							<li><a href="<?= Yii::app()->createUrl(Yii::app()->getModule('userAccount')->profileUrl); ?>"><?= Yii::t('frontend', 'Profile'); ?></a></li>
							<li><a href="<?= Yii::app()->createUrl(Yii::app()->getModule('userAccount')->editIdentityUrl); ?>"><?= Yii::t('frontend', 'Identity'); ?></a></li>
							<li class="divider"></li>
							<li><a href="<?= Yii::app()->createUrl(Yii::app()->getModule('userAccount')->logoutUrl); ?>">Log out</a></li>
						</ul>
				    </li>
				</ul>
				<?php else: ?>
				    <div class="navbar-form pull-right"> 
					<a href="<?= Yii::app()->createUrl(Yii::app()->getModule('userAccount')->loginUrl); ?>" class="btn">Sign in</a>
					<a href="<?= Yii::app()->createUrl(Yii::app()->getModule('userAccount')->registrationUrl); ?>" class="btn btn-success">Registration</a>
				    </div>
				<?php endif; ?>
			</div>
			<!--/.nav-collapse -->
		</div>
	</div>
</div>

<div class="container-fluid">
    <div class="flash-messages affix span4 offset8">
    <?php
    $this->widget('bootstrap.widgets.TbAlert', array(
	'block'=>true, // display a larger alert block?
	'fade'=>true, // use transitions?
	'closeText'=>'×', // close link text - if set to false, no close link is displayed
    ));
    ?>
    </div>
</div>

<div class="container-fluid">    
    
    <?php echo $content; ?>
    
    <hr>

    <footer>
            <p>&copy; Open Digital Society Foundation 2013</p>
    </footer>
</div>

</body>
</html>