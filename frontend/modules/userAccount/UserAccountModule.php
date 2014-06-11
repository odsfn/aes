<?php

/**
 * @todo 
 * - add Notification component which will encapsulate logic of messages sending
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class UserAccountModule extends CWebModule
{
    /**
     * @todo Provide basic layout which can be used in other projects
     * @var string Alias to layout using in module's controllers by default 
     */
    public $layout = 'application.views.layouts.column1';

    public $registrationFormClass = 'RegistrationForm';

    // --- URLS start ---
    public $registrationUrl = "/userAccount/registration";

    /**
     * This one for user's account activation. 
     * @TODO: 
     * - remove it, use confirmationUrl instead
     * @var string 
     */
    public $activationUrl = "/userAccount/registration/activate";

    public $confirmationUrl = "/userAccount/identity/confirm";

    public $recoveryUrl = "/userAccount/recovery";

    public $loginUrl = "/userAccount/login";

    public $logoutUrl = "/userAccount/login/out";

    public $profileUrl = "/userAccount/profile/edit";

    public $returnUrl = "/";

    public $returnLogoutUrl = "/userAccount/login";

    public $editIdentityUrl = "/userAccount/identity/edit";

//    public $afterIdentityEditedUrl = "/userAccount/profile";
    public $afterIdentityEditedUrl = "/userAccount/identity/edit";
    // --- URLS end ---
    
    // --- Views ---
    
    public $registrationView = 'registration';
    
    public $registrationFormView = 'userAccount.views.profile._form';
    // --- /Views ---
    
    public $rememberMeTime = 3600;

    public $userModelConfig = 'UserAccount';

    public $photosDir = '/uploads/photos';

    public $defaultPhoto = 'unknown_user.png';

    public $photoExtensions = array('png', 'jpg');

    public $photoMaxSize = 3000000;

    public $allowActivationOnPasswordReset = true;
    
    /**
     * Custom relations for Profile model
     * 
     * @var array 
     */
    public $profileCustomRelations = array();
    
    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'userAccount.models.*',
            'userAccount.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else
            return false;
    }

    /**
     * Resets user's password and send it to email
     * @param UserAccount $user
     */
    public function resetPassword(UserAccount $user)
    {
        if ($user->status != UserAccount::STATUS_ACTIVE) {
            if (!$this->allowActivationOnPasswordReset)
                throw new CException('Can\'t reset password for inactive users.');
            else {
                $identity = Identity::model()->findByAttributes(array(
                    'user_id' => $user->id,
                    'type' => Identity::TYPE_EMAIL,
                    'status' => Identity::STATUS_NEED_CONFIRMATION
                ));

                $identity->userIdentityConfirmation->confirm();
            }
        }

        $emailAddr = $user->getActiveEmail();

        $newPassword = $this->randomPassword();
        $user->setPassword($newPassword);
        $user->save(false, array('password'));

        $email = new YiiMailer('resetPassword', $data = array(
            'newPassword' => $newPassword,
            'description' => $description = 'Password reset'
        ));

        $email->setSubject($description);
        $email->setTo($emailAddr);
        $email->setFrom(Yii::app()->params['noreplyAddress'], Yii::app()->name, FALSE);

        Yii::log('Sendign reset password mail to ' . $emailAddr);

        if ($email->send())
            Yii::log('Ok');
        else {
            Yii::log('Failed');
            throw new CException('Failed to send the email');
        }
    }

    function randomPassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function __call($name, $parameters)
    {

        // magic for create
        if (preg_match('/^create(.+)$/', $name)) {
            return $this->_instantiateByConfig($name, $parameters);
        }

        return parent::__call($name, $parameters);
    }

    /**
     * Создает экземпляр класса по свойству данного модуля, которое содержит 
     * конфигурацию компонента Yii. 
     * 
     * Вызов $module->createProfileForm(), вернет компонент, конфигурация которого
     * находится в свойстве profileFormConfig
     * 
     * @param type $name    Имя метода
     * @param type $parameters  Параметры
     * @return type mixed
     */
    protected function _instantiateByConfig($name, $parameters)
    {
        $config = str_replace('create', '', $name, $replacements = 1);
        $config = lcfirst($config) . 'Config';
        $config = $this->$config;

        return Yii::createComponent($config);
    }

}
