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
	<title></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">

	<style>
		body {
			padding-top: 60px;
			padding-bottom: 40px;
		}
		
		body > div.navbar li.divider-vertical{
		    margin: 0px;
		}
	</style>

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
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

			<a class="brand" href="<?= Yii::app()->getBaseUrl(true); ?>">AES</a>

			<div class="nav-collapse collapse">
				<ul class="nav">
					<li class="divider-vertical"></li>
					<li class="active"><a href="#">About</a></li>
					<li class="divider-vertical"></li>
					<li><a href="#">Elections</a></li>
					<li class="divider-vertical"></li>
					<li><a href="#">People</a></li>
					<li class="divider-vertical"></li>
				</ul>
				
				<?php if(!Yii::app()->user->isGuest) :?>
				<ul class="nav pull-right">
				    <li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= Yii::app()->user->username; ?><b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="#">Profile</a></li>
							<li class="divider"></li>
<!--							<li class="nav-header">Nav header</li>-->
							<li><a href="#">Log out</a></li>
						</ul>
				    </li>
				</ul>
				<?php else: ?>
				    <div class="navbar-form pull-right"> 
					<a href="#" class="btn">Sign in</a>
					<a href="#" class="btn btn-success">Registration</a>
				    </div>
				<?php endif; ?>
			</div>
			<!--/.nav-collapse -->
		</div>
	</div>
</div>

<?php echo $content; ?>

</body>
</html>