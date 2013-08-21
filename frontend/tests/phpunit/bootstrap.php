<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../common/lib/vendor/yiisoft/yii/framework/yiit.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

require('./../../../common/lib/vendor/autoload.php');

//Yiinitializr\Helpers\Initializer::create('./../../../', 'console', array(
//	__DIR__ .'/../../../common/config/main.php',
//	__DIR__ .'/../../../common/config/env.php',
//	__DIR__ .'/../../../common/config/local.php',
//	'main',
//	'env',
//	'local-test'
//));

$config = Yiinitializr\Helpers\Initializer::config('frontend', array(
	__DIR__ .'/../../../common/config/main.php',
	__DIR__ .'/../../../common/config/env.php',
	__DIR__ .'/../../../common/config/local.php',
	'main',
	'test',
	'local-test'
));

$app = \Yii::createWebApplication($config);

// Migrate up for the test db
$runner=new CConsoleCommandRunner();
$consoleCommands = require __DIR__ .'/../../../console/config/console.php';
$runner->commands = array(
    'migrate' => array_merge(
            $consoleCommands['commandMap']['migrate'], 
            array('interactive' => false)
     )
);

$runner->run(array(
    'yiic',
    'migrate',
));

// Any file-system dependent data preparations. For example renaming user's uploads
// folder, and creating another with testing images

/** 
 * @TODO: Start selenium server, if it is not running
 * 
 * It may be done using command like this. But we have to redirect output to another
 * stream. If we'll not do this php script running will nang
 * 
 * system('java -jar /var/www/selenium-server-standalone-2.35.0.jar');
 * 
 */