Installation
=======

#For development

0. Install Composer [http://getcomposer.org/]. Or run: 

/var/www/aes/app$ php composer.phar install

1. Get AES from repository

2. Create user and database for the system. Write it into the common/config/local.php db section. Note, user have to be able
to do all operations with this database. 

Please consider that DB will be with correct CHARSET utf8 and COLLATION utf8_general_ci. You may just to run statement:

CREATE DATABASE aes DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
 
3. Provide other environmental setting in the common/config/local.php file

4. Run the command from corresponding location: 

/var/www/aes/app$ composer install

All dependencies will be fetched, and installation process will start