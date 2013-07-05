Installation
=======

#For development

0. Get AES from repository

1. Install Composer [http://getcomposer.org/]. Or run: 

/var/www/aes/app$ php composer.phar install

2. Create user and database for the system. Write it into the common/config/local.php db section ( see examples below ). Note, user have to be able
to do all operations with this database. 

Please consider that DB will be with correct CHARSET utf8 and COLLATION utf8_general_ci. You may just to run statement:

CREATE DATABASE aes DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
 
3. Provide other environmental setting in the common/config/local.php file

4. Run the command from corresponding location: 

/var/www/aes/app$ composer install

All dependencies will be fetched, and installation process will start. It will ask you in which environment you want to deploy.

5. Run 

/var/www/aes$ ./yiic migrate up --migrationPath=webroot.frontend.modules.userAccount.migrations

##Examples

common/config/local.php: 

<?php
return array(
	'components' => array(
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=aes',
			'username' => 'root',
			'password' => 'root',
			'initSQLs' => array('SET time_zone = "Europe/Kiev";'),
		),
	    'log'=>array(
		'routes'=>array(
		    'info_log' => array(
			'class'=>'CFileLogRoute',
			'logFile'=>'application.log',
//			'levels'=>'info, trace',
			'levels'=>'info',
		    ),
		),
	    )
	)
);


frontend/config/local.php: 

<?php
/*
 * Local configuration setting for your ( developer's ) PC and for frontend application. 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
return array(	    
    'components'=>array(
	'log'=>array(
		'routes'=>array(
		    'web_log' => array(
//			'levels'=>'error, warning, info, trace',
			'levels'=>'error, warning, info',
			'enabled'=>true
		    ),
		),
	    )
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