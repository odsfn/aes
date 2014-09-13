Installation
=======

System requirements

PHP 5.3
Mysql 5.1 - 5.5

On staging Mysql version is
 5.1.73-cll - MySQL Community Server (GPLv2)

On main developer's machine:
 PHP 5.3.10-1ubuntu3.11
 5.5.37-0ubuntu0.12.04.1 - (Ubuntu)

#For development

0. 

Get AES from repository
Check that you have installed mysql timezones, if not follow this instructions http://dev.mysql.com/doc/refman/5.1/en/mysql-tzinfo-to-sql.html

1. Install Composer [http://getcomposer.org/]. You may skip this step, but on the 4 step you shuld run: 

/var/www/aes/app$ php ./composer.phar install

2. Create user and database for the system. Write it into the common/config/local.php db section ( see examples below ). Note, user have to be able
to do all operations with this database. 

Please consider that DB will be with correct CHARSET utf8 and COLLATION utf8_general_ci. You may just to run statement:

CREATE DATABASE aes DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
 
3. Provide other environmental setting in the common/config/local.php file

4. Run the command from corresponding location: 

/var/www/aes/app$ composer install

OR ( id you did not install composer into the system ):

/var/www/aes/app$ php ./composer.phar install 

All dependencies will be fetched, and installation process will start. It will ask you in which environment you want to deploy.

At the last step it will updates all migrations.

5. You should create 2 virtual hosts config for apache
First is for electors and visitors (front-office). DocumentRoot should point to frontend/www/
Second is for superadmin, admins and managers (back-office). DocumentRoot should point to backend/www/
Each of them should contain directives:
	
        RewriteEngine On
	RewriteRule ^/ui(.*) /var/www/aes/common/ui$1

To correctly use common ui components. You also should replace "/var/www/aes/" to
the correct path where AES is deployed

##Examples

common/config/local.php: 

<?php
/*
 * Local configuration setting for your ( developer's ) PC. 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
return array(
	'components' => array(
            // Specify your database here
            'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=aes',
                    'username' => 'root',
                    'password' => 'root',
                    'initSQLs' => array('SET storage_engine=INNODB; SET time_zone = "Europe/Kiev";'),
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
//			'levels'=>'info, trace',
//                        'categories'=>'system.db.*'
			'levels'=>'info',
		    ),
		),
	    )
	),
    
    'params' => array(
            'php.error_reporting' => E_ERROR | E_WARNING | E_PARSE,
        
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
            )
    )
);

frontend/config/local.php: 

<?php
/**
 * Custom config options for frontent apps
 */
return array(	    
    'components'=>array(
	'log'=>array(
		'routes'=>array(
		    'web_log' => array(
                        'class'=>'CWebLogRoute',
			'levels'=>'error, warning, info, trace',
//			'levels'=>'error, warning, info',
			'enabled'=>false
		    ),
		),
	    )
    )
);

## Дополнительные детали о конфигурации

Используется шаблон структуры проекта https://github.com/tonydspaniard/yiinitializr-intermediate 
Используется библиотека https://github.com/2amigos/yiinitializr

Пояснения из переписки: 

[10:06:33] Snoop&Stalk: Привет. Слушай я глянул структуру проекта. Она оказалась немного непривычной. Подскажи мне немножко как ее правильно развернуть:
- у нас обычно в каталоге сайта framework, protected и httpdocs. У аиса framework я не нашел, протектед судя по всему и есть рут, а хттпдокс разделен между фронт и бекендом. Подразумевается что framework лежит где то выше? а для фронт и бекенда отдельные сайты?
[10:07:06] Василий: сейчас расскажу
[10:07:32] Василий: рут для фронтенда frontend/www
[10:07:50] Snoop&Stalk: - в протектед у нас еще обычно 2 конфига - локальный (для хоста) и общий - в котором задаются настройки не зависящие от места развертывания
[10:08:10] Snoop&Stalk: ок
[10:11:12] Василий: по конфигам ситуация такая они лежат в таких папках: 

backand/config
common/config
frontend/config

Начнем с common/config - это конфигурация ( подключение модулей, настройка компонентов общих как для фронтенда , так и для бекенда ) 
main.php
env.php 
local.php

Файлы конфигураций мержаться в таком порядке main.php < env.php < local.php
[10:11:46] Василий: в local.php - находятся настройки системы для девелоперской машины ( у каждого свой локал, и мы его в системе версий не храним )
[10:12:05] Василий: сюда настройки своей базы, настройки уровня и способов логирования, временной зоны и т.п
[10:14:02] Василий: env.php - конфиги среды. Девелоперская / стейджинг / продакшн. Надо запомнить, что этот файл мы не пишем и не изменяем руками. Его содержимое поялвяется автоматически ( берется из config/env соответсвующий файл, имя которого совпадает с именем среды ).
[10:14:43] Василий: он пишется/создается когда мы выполняем aes/$ composer install или update
[10:14:54] Василий: он кстати спросит, какую среду развернуть
[10:15:00] Snoop&Stalk: угу 
еще момент - при автодеплое я обычно создаю\очищаю следующие каталоги
httpdocs/assets
httpdocs/protected/runtime
[10:16:07] Василий: т.е. при автодеплое надо дергать composer update. Он как раз создает папки assets и runtime в случае их отсутсвия
[10:16:23] Василий: в frontend/ и backend/
[10:16:58] Василий: и он же вытягивает все зависимости ( фреймверк , сторонние модули ) , которые указаны в composer.json
[10:18:31] Василий: по конфигурации еще: 

сливается все в такой последовательности common/config/main.php < common/config/env.php < common/config/local.php < frontend/config/frontend.php < frontend/config/env.php < frontend/config/local.php
[10:20:22] Василий: если развернул сначала в дев среде, а надо переключится на продакшн - делаешь: 
1. удалить common/lib/Yiinitializr/config/env.lock 
2. удалить common|frontend|backend/config/env.php
[10:21:19] Snoop&Stalk: понятно. а дергать composer как? как то phar запускать из командной строки?
[10:21:30] Василий: есть 2 варианта
[10:21:42] Василий: можно установить его usr/bin
[10:21:52] Василий: и дергать как консольную команду
[10:21:56] Василий: или как фар
[10:21:59] Василий: архив
[10:22:02] Василий: сейчас дам ссылку
[10:22:49] Василий: установка для варианта 1. http://getcomposer.org/doc/00-intro.md#installation-nix
[10:24:34] Василий: если устанавливать не хочешь то так: 
$ php composer.phar install - вытягивает все зависимости, устанавливает их, выполняет миграции
[10:24:57] Василий: создает папки для ассетов и рантайм
[10:25:17] Василий: создает env.php
[10:25:32] Snoop&Stalk: а фронт и бекенд - отдельными сайтами по сути встают да?
[10:26:10] Василий: да у них будут разные точки входа, можно бекенд субдоменом например
[10:26:13] Василий: база общая
[10:26:18] Snoop&Stalk: я к тому что у апача я wwwroot настраиваю на frontend/www
[10:26:31] Snoop&Stalk: ну и на продакшне у nginx Тоже
[10:26:47] Snoop&Stalk: в целом понятно. Придется мне новый скрипт развертывыания правда написать. Но это ничего
[10:26:53] Василий: да . пока на только frontend/www в бекенде у нас пока пусто
[10:27:37] Snoop&Stalk: еще момент.. как в этой структуре интегрировать вордпресс уже не совсем понятно ) Я научился это делать только для обычной пока что
[10:29:16] Василий: frontend/www 
 тут у нас лежат файлы доступные через веб ( ассеты, фронт-контроллер ( index.php ) )
 в принципе по простому wp по идее можно слить в www. Зависимостей то в коде у нас нет, верно?
[10:31:14] Василий: можно так же попробовать у wp вынести сюда aes/wp 
index.php WP , переименовать и закинуть в www/wp-index.php, обновить в нем пути подключения кода вордпресса
