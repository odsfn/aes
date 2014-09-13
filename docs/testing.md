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

# provide common/config/local-test.php configurations with settings of your testing database and testing host.
This file will be loaded instead common/comfig/local.php when we accessing testing
system instance ( through index-test.php )

local-test.php file contents example: 

<?php
/*
 * Local configuration setting for your ( developer's ) PC 
 * for testing application ( through index-test.php ) 
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
            'initSQLs' => array('SET storage_engine=INNODB; SET time_zone = "Europe/Kiev"; SET time_zone = "Europe/Kiev";'),
        ),
        
        'log'=>array(
            'routes'=>array(
                'error_log' => array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'error.log',
                    'levels'=>'error, warning',
                    'filter'=>'CLogFilter',
                ),
                'info_log' => array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'application.log',
//                  'levels'=>'info, trace',
//                  'categories'=>'system.db.*'
                    'levels'=>'info',
                ),
            ),
        )        
    ),
    
    'params'=>array(
	
        'php.error_reporting' => E_ERROR /*| E_WARNING | E_PARSE*/,
        
        'yii.handleErrors' => true,
        'yii.debug' => true,    //switch this option to disable debug mode
        'yii.traceLevel' => 3,

        'noreplyAddress'=>'vptester@mail.ru',        

        'YiiMailer'=>array(
            'Mailer'=>'smtp',
            'Host'=>'smtp.mail.ru',
            'Port'=>'2525',
            'Username'=>'vptester@mail.ru',
            'Password'=>'vptester_qwerty',
            'SMTPAuth'=>true,
            
            'SMTPDebug'=> 2,
	    'savePath' => 'application.runtime',
	    'testMode' => true,            
        )
    )
);

then run command:

vasiliy_pdk@vazia-Inspiron-7720:/var/www/aes/frontend/tests/phpunit$ phpunit functional/

All migrations will be applied to the database. Testing process will begin
