<?php
/**
 *
 * main.php configuration file
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
return array(
	'preload' => array('log'),
	'aliases' => array(
		'frontend' => dirname(__FILE__) . '/../..' . '/frontend',
		'common' => dirname(__FILE__) . '/../..' . '/common',
		'backend' => dirname(__FILE__) . '/../..' . '/backend',
		'vendor' => dirname(__FILE__) . '/../..' . '/common/lib/vendor',
	),
	'import' => array(
		'common.extensions.components.*',
		'common.components.*',
		'common.helpers.*',
		'common.models.*',
		'application.controllers.*',
		'application.extensions.*',
		'application.helpers.*',
		'application.models.*',
	),
	'components' => array(
	    
		'db'=>array(
		    'connectionString' => 'mysql:host=localhost;dbname=aes',
		    'emulatePrepare' => true,
		    'username' => 'root',
		    'password' => 'root',
		    'charset' => 'utf8',
		    'tablePrefix' => '',
		    'initSQLs' => array('SET time_zone = "Europe/Kiev";')
		),
	    
		'errorHandler' => array(
			'errorAction' => 'site/error',
		),
	    
	    	'log'=>array(
		    'class'=>'CLogRouter',
		    'routes'=>array(
			    'error_log' => array(
				    'class'=>'CFileLogRoute',
				    'logFile'=>'error.log',
				    'levels'=>'error, warning',
				    'filter'=>'CLogFilter'
			    ),
		    ),
		),
	    
	),
    
	'params' => array(
		// php configuration
		'php.defaultCharset' => 'utf-8',
		'php.timezone'       => 'Europe/Kiev',
//		'php.error_reporting' => 22519,
	    
	    'yii.handleErrors' => true,
		'yii.debug' => true,
		'yii.traceLevel' => 4,
	)
);
