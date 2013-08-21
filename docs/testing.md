To run automated tests you should 

# install PHPUnit >= 3.7.24

http://phpunit.de/manual/3.7/en/installation.html

with PHPUnit Selenium
and DbUnit

# get selenium-server

http://docs.seleniumhq.org/download/

direct link to download http://selenium.googlecode.com/files/selenium-server-standalone-2.35.0.jar

# start selenium-server

If you want to run functional tests start selenium-server:

vasiliy_pdk@vazia-Inspiron-7720:~$ java -jar /var/www/selenium-server-standalo-2.35.0.jar

Note, that you should specify correct path to the downloaded selenium-server

# provide frontend/config/local-test.php configurations with settings of your testing database and testing host

local-test.php file contents example: 

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

then run command:

vasiliy_pdk@vazia-Inspiron-7720:/var/www/aes/frontend/tests/phpunit$ phpunit functional/

All migrations will be applied to the database. Testing process will begin
