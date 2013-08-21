<?php
/*
 * Common configuration for testing version of the application.
 * 
 * You should provide testing database settings in your local-test.php. For example
 * 
 * return array(	    
 *   'components'=>array(
 *     . . .  
 *       'db' => array(
 *           'connectionString' => 'mysql:host=localhost;dbname=aes_test',
 *           'username' => 'root',
 *           'password' => 'root',
 *           'initSQLs' => array('SET time_zone = "Europe/Kiev";'),
 *       ),
 *     . . .    
 *   ),
 * 
 *   . . .
 * 
 * );
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
return array(
	'modules' => array(
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => 'yii',
			'ipFilters' => array('127.0.0.1','::1'),
		),
	),
        'components'=>array(
	    'log'=>array(
		'routes'=>array(
		    'web_log' => array(
			'class'=>'CWebLogRoute',
			'levels'=>'error, warning, info',
//			'levels'=>'error, warning, info, trace',
			// set to true in your frontend/config/local the following to show log messages on web pages
			'enabled'=>false
		    ),
		),
	    ), 
            
            'assetManager' => array(
                'forceCopy' => true
            ),
            
            'urlManager' => array(
//                    'urlFormat' => 'path',
                    'showScriptName' => true,

//                    'rules' => array(
//                            // default rules
//                            '<controller:\w+>/<id:\d+>' => '<controller>',
//                            '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
//                            '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
//                    ),
            ),
	)
);
