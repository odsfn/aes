Automated election system
=======
Automated election system ( AES ) is the new approach for consolidation of democracy. This system allows to run elections cleaner, cheaper and more comfortable for electorate as well as for candidates. Furthermore the system organizes more efficient process of interaction between deputies and citizens.

#Technical information for developers
Information about installation you can find in the /docs folder.

Below is the information about project structure

##YIInitializr-intermediate

The following is a proposed project structure for basic to intermediate applications that works in conjunction with YIInitializr components. 

YIInitializr vanilla projects make extensive use of Composer. We found at [2amigos.us](http://2amigos.us) that is easier to your extensions bundle outside of your application scope - thanks to [Phundament](http://phundament.com) and Tobias Munk for his knowledge and shares. Composer is your dependency package best friend. 

The package is cleaned from extensions, you choose what you wish to include in your composer.json files. The only ones included are Yii Framework (obviously), [YiiStrap](https://github.com/yii-twbs/yiistrap) and [YiiWheels](https://github.com/2amigos/yiiwheels), the rest is up to you. We do not want to confuse you. 


## Setup and first run

  * Set up Git by following the instructions [here](https://help.github.com/articles/set-up-git).
  * Update the configurations in `api/config/`, `frontend/config/`, `console/config/`, `backend/config/` and `common/config/` to suit your needs. The `common/config/main.php` is configured to use **sqllite** by default. Change your `common/config/env/dev.php` to suit your database requirements.
 * Composer is required The package includes already a `composer.phar` file. 
 * Browse through the `composer.json` and remove the dependencies you don't need also update the required versions of the extensions.
 * If you have `composer` installed globally:
	 * Run `composer self-update` to make sure you have the latest version of composer. 
	 * Run `composer install` to download all the dependencies.
 * If you work the `composer.phar` library within the project boilerplate. 
 	 * Run `php composer.phar self-update` to make sure you have the latest version of composer. 
	 * Run `php composer.phar install` to download all the dependencies.
 * `Yiinitializr\Composer\Callback` will configure everything required on your application: `runtime` and `assets` folders and migrations.


For more information about using Composer please see its [documentation](http://getcomposer.org/doc/).

###How to configure the application

This boilerplate is very similar to YiiBoilerplate but differs from it for the easiness of its configuration. We focused to release the pain of configuring your application and combine your configuration files. `Yiinitializr\Helpers\Initializr` is very easy to use, check for example the bootstrap `index.php` file at the frontend:

```
require('./../../common/lib/vendor/autoload.php');
require('./../../common/lib/vendor/yiisoft/yii/framework/yii.php');

Yii::setPathOfAlias('Yiinitializr', './../../common/lib/Yiinitializr');

use Yiinitializr\Helpers\Initializer;

Initializer::create('./../', 'frontend', array(
	__DIR__ .'/../../common/config/main.php', // files to merge with
	__DIR__ .'/../../common/config/env.php',
	__DIR__ .'/../../common/config/local.php',
))->run();
```

For more information about Yiinitializr please check it at [its github repo](https://github.com/2amigos/yiinitializr).

## Overall Structure

Bellow the directory structure used:

```
   |-backend
   |---components
   |---config
   |-----env
   |---controllers
   |---extensions
   |---helpers
   |---lib
   |---models
   |---modules
   |---tests
   |---views
   |-----layouts
   |-----site
   |---widgets
   |---www
   |-----css
   |-------fonts
   |-----img
   |-----js
   |-------libs
   |-common
   |---components
   |---config
   |-----env
   |---extensions
   |-----components
   |---helpers
   |---lib
   |-----Yiinitializr
   |-------Cli
   |-------Composer
   |-------Helpers
   |-------config
   |---messages
   |---models
   |---schema
   |---widgets
   |-console
   |---commands
   |---components
   |---config
   |-----env
   |---data
   |---extensions
   |---migrations
   |---models
   |-frontend
   |---components
   |---config
   |-----env
   |---controllers
   |---extensions
   |---helpers
   |---lib
   |---models
   |---modules
   |---tests
   |---views
   |-----layouts
   |-----site
   |---widgets
   |---www
   |-----css
   |-------fonts
   |-----img
   |-----js
   |-------libs
```

Copyright © 2015 Open Digital Society Foundation

Distributed under the GNU GPL v2.0