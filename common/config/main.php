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
                'console' => dirname(__FILE__) . '/../..' . '/console',
		'vendor' => dirname(__FILE__) . '/../..' . '/common/lib/vendor',
		'stateMachine' => dirname(__FILE__) . '/../..' . '/common/lib/vendor/vasiliy-pdk/YiiStateMachine',
	),
	'import' => array(
		'common.extensions.components.*',
		'common.components.*',
		'common.helpers.*',
		'common.models.*',
            
		'application.controllers.*',
		'application.components.*',
		'application.extensions.*',
		'application.helpers.*',
		'application.models.*',
                'application.modules.personIdentifier.models.PersonIdentifier',
                'application.modules.userAccount.models.UserAccount',
            
		'common.extensions.YiiMailer.YiiMailer'
	),
	'components' => array(
	    
                'authManager'=>array(
                    'class'=>'common.components.AuthManager',
                    'connectionID'=>'db',
                    'defaultRoles' => array(
                        'commentReader', 'commentor', 'authenticated', 'guest',
                        'superadmin'
                    )
                ),            
            
		'db'=>array(
//		    Provide this setting in the config/local.php or config/env/dev.php or config/env/prod.php
//		    
//		    'connectionString' => 'mysql:host=localhost;dbname=_yourDBName_',
//		    'username' => 'root',
//		    'password' => 'root',
//		    'initSQLs' => array('SET storage_engine=INNODB; SET time_zone = "Europe/Kiev";'),
		    
                    'pdoClass' => 'NestedPDO',
		    'emulatePrepare' => true,
		    'charset' => 'utf8',
		    'tablePrefix' => '',
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

		'image' => array(
		    'class'  => 'common.extensions.image.CImageComponent',
		    'driver' => 'GD',                               // если ImageMagick, надо указать к нему путь ниже
		    'params' => array( 'directory' => '/usr/bin' ), // в этой директории должен быть convert
		),
            
                'tubeLink' => array(
                    'class' => 'common.components.CTubeLink'
                )
	),
    
	'params' => array(
		// php configuration
		'php.defaultCharset' => 'utf-8',
		'php.timezone'       => 'Europe/Kiev',
		'php.error_reporting' => 22519,
	    
		'yii.handleErrors' => true,
		'yii.debug' => true,
		'yii.traceLevel' => 4,
		
                'ext_debug' => false,   //all extjs client apps will be loaded in prodaction mode if this set to false
            
		'noreplyAddress' => 'noreply@aes.org',
	    
                // superadmin is the user which has full access to the system
                // here is specified user id
                'superAdminId' => 1,
            
                // mailer config
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
