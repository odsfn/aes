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
	'controllerMap' => array(
        'gallery'=>'ext.galleryManager.GalleryController',
    ),

    'import' => array(
        'userAccount.models.UserAccount',
        'userAccount.models.Profile',
        'ext.galleryManager.models.*',
        'ext.galleryManager.*',
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
			'urlFormat' => 'path',
			'showScriptName' => false,

			'rules' => require('in-frontend/rules.php')
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
                    'packages' => require('in-frontend/packages.php')
                ),
            
                'widgetFactory' => array(
                    
                    'class' => 'AesWidgetFactory',
                    
                    'widgets' => require('in-frontend/widgets.php')

                ),
	),
    
	'modules' => array(
	    'userAccount' => array(
                'controllerMap' => array(
                    'registration' => array(
                        'class' => 'frontend.controllers.AesRegistrationController'
                    )
                ),
		'returnUrl' => '/userPage/index',
                'registrationFormView' => 'frontend.views.userAccount.profile._form',
                'registrationView' => 'frontend.views.userAccount.registration.registration'
	    ),
	    
	    'gii' => array(
		'generatorPaths' => array('bootstrap.gii'),
	    ),
            
            'api',
            
            'personIdentifier' => require('in-frontend/personIdentifier.php')
	),
    
        'params' => array(
            'RESTusername' => 'admin@restuser',
            'RESTpassword' => 'admin@Access',
        )
);