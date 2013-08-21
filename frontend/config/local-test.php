<?php
/*
 * Local configuration setting for your ( developer's ) PC and for frontend testing application. 
 * @author Vasiliy Pedak truvazia@gmail.com
 */

/**
 * Change the following URL based on your server configuration
 * Make sure the URL ends with a slash so that we can use relative URLs in test cases
 */
define('TEST_BASE_URL','http://aes.dev/index-test.php/');

return array(	    
    'components'=>array(
        
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=aes_test',
            'username' => 'root',
            'password' => 'root',
            'initSQLs' => array('SET time_zone = "Europe/Kiev";'),
        ),
        
    ),
    
    'params'=>array(
	
	'noreplyAddress'=>'vptester@mail.ru',
	
	'YiiMailer'=>array(
//	    'SMTPDebug'=>2
//	    'savePath' => 'application.runtime',
//	    'testMode' => true,
	)
    )
);