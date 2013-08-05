<?php
/**
 *
 * frontend.php configuration file
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
defined('APP_CONFIG_NAME') or define('APP_CONFIG_NAME', 'frontend');

// web application configuration
return array(
	'name' => 'Automated Election System',
	'basePath' => realPath(__DIR__ . '/..'),
    
	'preload'=>array(
	    'bootstrap',
	),
	// path aliases
	'aliases' => array(
		'bootstrap' => dirname(__FILE__) . '/../..' . '/common/lib/vendor/vasiliy-pdk/YiiBooster/src',
		'userAccount' => dirname(__FILE__) . '/../modules/userAccount/'
	),
    
	// application behaviors
	'behaviors' => array(),

	// controllers mappings
	'controllerMap' => array(),

	// application components
	'components' => array(

		'bootstrap' => array(
		        'class' => 'bootstrap.components.Bootstrap',
			'responsiveCss' => true,
			'yiiCss' => false,
			'fontAwesomeCss' => true
		),
	    
		'urlManager' => array(
			// uncomment the following if you have enabled Apache's Rewrite module.
			'urlFormat' => 'path',
			'showScriptName' => false,

			'rules' => array(
				// default rules
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
		),
	    
		'user' => array(
		    'allowAutoLogin' => true,
		    'loginUrl' => array('userAccount/login')
		),
	    
		'errorHandler' => array(
			'errorAction' => 'site/error',
		)
	),
    
	'modules' => array(
	    'userAccount',
	    
	    'gii' => array(
		'generatorPaths' => array('bootstrap.gii'),
	    ),
	),
);