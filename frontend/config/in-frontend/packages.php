<?php
/**
 * Contains packages description. Will be included in frontend.php config file
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
return array(

    'qunit' => array(
        'baseUrl' => '//code.jquery.com/qunit/',
        'js' => array('qunit-1.14.0.js'),
        'css'=> array('qunit-1.14.0.css')
    ),
    
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

    'backbone.validation' => array(
        'depends' => array('backbone'),
        'baseUrl' => 'js/libs/backbone.validation',
        'js' => array(
            'backbone-validation.js',
            'aes-validators.js'
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
    ),

    'backbone.poller' => array(
        'depends' => array('backbone'),

        'baseUrl' => 'js/libs/backbone.poller',
        'js' => array(
            'backbone.poller.js'
        )
    )
);
