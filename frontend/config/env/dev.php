<?php
/**
 *
 * dev.php configuration file
 * 
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
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
            )
	)
);