<?php
/**
 *
 * Yiic.php bootstrap file will instantiate application with test configuration
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
define('TEST_APP_INSTANCE', true);

require('./common/lib/vendor/autoload.php');

Yiinitializr\Helpers\Initializer::create('./console', 'console', array(
	'./common/config/main.php',
	'./common/config/env.php',
	'./common/config/local-test.php'
))->run();

