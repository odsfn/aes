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
		'userAccount' => dirname(__FILE__) . '/../modules/userAccount/',
                'ext'   => __DIR__ . '/../../common/extensions/'
	),
    
	// application behaviors
	'behaviors' => array(),

	// controllers mappings
	'controllerMap' => array(),
        
        'import' => array(
            'userAccount.models.UserAccount',
            'userAccount.models.Profile'
        ),
    
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
                                // Restfullyii routes
                                'api/<controller:\w+>'=>array('api/<controller>/restList', 'verb'=>'GET'),
                                'api/<controller:\w+>/<id:\w*>'=>array('api/<controller>/restView', 'verb'=>'GET'),
                                'api/<controller:\w+>/<id:\w*>/<var:\w*>'=>array('api/<controller>/restView', 'verb'=>'GET'),
                                'api/<controller:\w+>/<id:\w*>/<var:\w*>/<var2:\w*>'=>array('api/<controller>/restView', 'verb'=>'GET'),

                                array('api/<controller>/restUpdate', 'pattern'=>'api/<controller:\w+>/<id:\w*>', 'verb'=>'PUT'),
                                array('api/<controller>/restUpdate', 'pattern'=>'api/<controller:\w+>/<id:\w*>/<var:\w*>', 'verb'=>'PUT'),
                                array('api/<controller>/restUpdate', 'pattern'=>'api/<controller:\w*>/<id:\w*>/<var:\w*>/<var2:\w*>', 'verb'=>'PUT'),   

                                array('api/<controller>/restDelete', 'pattern'=>'api/<controller:\w+>/<id:\w*>', 'verb'=>'DELETE'),
                                array('api/<controller>/restDelete', 'pattern'=>'api/<controller:\w+>/<id:\w*>/<var:\w*>', 'verb'=>'DELETE'),
                                array('api/<controller>/restDelete', 'pattern'=>'api/<controller:\w+>/<id:\w*>/<var:\w*>/<var2:\w*>', 'verb'=>'DELETE'),

                                array('api/<controller>/restCreate', 'pattern'=>'api/<controller:\w+>', 'verb'=>'POST'),
                                array('api/<controller>/restCreate', 'pattern'=>'api/<controller:\w+>/<id:\w+>', 'verb'=>'POST'),                            
                            
				// default rules
				'<controller:\w+>/<id:\d+>' => '<controller>',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
		),
	    
		'user' => array(
		    'class' =>	'application.modules.userAccount.components.UAccWebUser',
		    'allowAutoLogin' => true,
		    'loginUrl' => array('userAccount/login')
		),
	    
		'errorHandler' => array(
			'errorAction' => 'site/error',
		),
            
                'clientScript' => array(
                    'class' => 'CClientScript',
                    'packages' => array(
                        
                        'aes-common' => array(
                            'depends' => array('marionette'),
                            'baseUrl' => 'js/libs/aes',
                            'js' => array(
                                'helpers.js',
                                'WebUser.js',
                                'i18n.js'
                            )
                        ),
                        
                        'marionette' => array(
                            'depends' => array(
                                'backbone'
                            ),
                            
                            'baseUrl' => 'js/libs/backbone.marionette',
                            'js' => array(
                                'backbone.marionette.js'
                            )
                        ), 
                        
                        'backbone' => array(
                            'depends' => array('jquery.ui'),
                            'baseUrl' => 'js/libs/backbone.marionette',
                            'js' => array(
                                'json2.js',
                                'underscore.js',
                                'backbone.js'
                            )
                        ),
                        
                        'loadmask' => array(
                            'depends' => array('jquery'),
                            'baseUrl' => 'js/libs/loadmask',
                            'js' => array(
                                'loadmask.js'
                            ),
                            'css' => array(
                                'loadmask.css'
                            )
                        )
                    )
                )
	),
    
	'modules' => array(
	    'userAccount' => array(
		'returnUrl' => '/userPage/index'
	    ),
	    
	    'gii' => array(
		'generatorPaths' => array('bootstrap.gii'),
	    ),
            
            'api'
	),
    
        'params' => array(
            'RESTusername' => 'admin@restuser',
            'RESTpassword' => 'admin@Access',
        )
);