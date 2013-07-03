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
	    
		'common.extensions.YiiMailer.YiiMailer'
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
		'php.error_reporting' => 22519,
	    
		'yii.handleErrors' => true,
		'yii.debug' => true,
		'yii.traceLevel' => 4,
		
		'noreplyAddress' => 'noreply@aes.org',
	    
		'YiiMailer'=>array(
//			'Mailer'=>'smtp',
//			'Host'=>'smtp.mail.ru',
//			'Port'=>'2525',
//			'Username'=>'****@mail.ru',
//			'Password'=>'****',
//			'SMTPAuth'=>true,
		    
			'exceptions'=>true,
		    
		        'viewPath' => 'application.views.mail',
			'layoutPath' => 'application.views.layouts.mail',
			'baseDirPath' => 'webroot.images.mail',
			'savePath' => 'webroot.assets.mail',
			'testMode' => false,
			'layout' => 'mail',
			'CharSet' => 'UTF-8',
			'AltBody' => 'You need an HTML capable viewer to read this message.',
			'language' => array(
				    'authenticate'         => 'SMTP Error: Could not authenticate.',
				    'connect_host'         => 'SMTP Error: Could not connect to SMTP host.',
				    'data_not_accepted'    => 'SMTP Error: Data not accepted.',
				    'empty_message'        => 'Message body empty',
				    'encoding'             => 'Unknown encoding: ',
				    'execute'              => 'Could not execute: ',
				    'file_access'          => 'Could not access file: ',
				    'file_open'            => 'File Error: Could not open file: ',
				    'from_failed'          => 'The following From address failed: ',
				    'instantiate'          => 'Could not instantiate mail function.',
				    'invalid_address'      => 'Invalid address',
				    'mailer_not_supported' => ' mailer is not supported.',
				    'provide_address'      => 'You must provide at least one recipient email address.',
				    'recipients_failed'    => 'SMTP Error: The following recipients failed: ',
				    'signing'              => 'Signing Error: ',
				    'smtp_connect_failed'  => 'SMTP Connect() failed.',
				    'smtp_error'           => 'SMTP server error: ',
				    'variable_set'         => 'Cannot set or reset variable: '
			),
		)
	)
);
