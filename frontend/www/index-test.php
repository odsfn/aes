<?php
/**
 * Entry point for testing version of the frontend application
 */
define('TEST_APP_INSTANCE', true);

require('./../../common/lib/vendor/autoload.php');

Yiinitializr\Helpers\Initializer::create('./../', 'frontend', array(
	__DIR__ .'/../../common/config/main.php',
	__DIR__ .'/../../common/config/env.php',
	__DIR__ .'/../../common/config/local.php',
	'main',
	'test',
	'local-test'
))->run();
