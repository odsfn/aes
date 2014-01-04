<?php
/*
 * This widget assembles specified client application accordingly with system conventions
 * 
 * Application is located in the subdirectory of the current action. 
 * For instanse frontend.views.[currentControllerId].assets.[currentActionId].js.[applicationName]
 */
class ClientApp extends CWidget {
    /**
     * Flag that indicates whether to run application in isolated mode. If true
     * all server requests will be collected by fake server object on client side
     * @var boolean 
     */
    public $isolated = false;
    /**
     * The client application name. Should be equals to the folder name in the
     * application.views.[controller].[action].assets.[js].[appName]
     * @var string 
     */
    public $appName;
    /**
     * List of the required files. See the Yii Packages configuration format.
     * You also can specify aes:...some_path... to load some common collections
     * models or other components
     * 
     * To this array will be added several paths automatically.  
     * @var array 
     */
    public $requires = array();
    /**
     * Scripts that will be applied as application initializer
     * @var array 
     */
    public $initializers = array();
    /**
     * @var CClientScript 
     */
    protected $clientScript;
    
    public function run() {

        $this->clientScript = Yii::app()->clientScript;

        $this->clientScript->registerPackage('aes-common');
        if(defined('TEST_APP_INSTANCE') && TEST_APP_INSTANCE) {
            $this->clientScript->registerScript('urlMangerInit', 
                    'UrlManager.setBaseUrl("/index-test.php");',
                    CClientScript::POS_HEAD
            );
        }
        
        //Registering backbone + marionete
        $this->clientScript->registerPackage('marionette');
        $this->clientScript->registerScriptFile('/js/libs/aes/i18n.js');
        $this->clientScript->registerScriptFile('/js/libs/jquery.dateFormat-1.0.js');
        
        /**
         * Resolving conflict with jquery.ui.button and bootstrap.button plugins
         * Bootstrap.button will be available by $().bButton
         * 
         * NOTE: You should update js/libs/bootstrap.button.js with the same version
         * of bootstrap if you are updating bootstrap.js
         */
        $this->clientScript->registerScriptFile('/js/libs/bootstrap.button.js', CClientScript::POS_END);
        $this->clientScript->registerScript('resolveBtnConflict', 
           '$(function(){ var btn = $.fn.button.noConflict();
            $.fn.bButton = btn; });', CClientScript::POS_END);
        
        $user = Yii::app()->user;
        
        /**
         * Initializing webUser for client-side
         */
        $this->clientScript->registerScriptFile('/js/libs/aes/WebUser.js');
        $this->clientScript->registerScript('webUserInit', 
            'WebUser.initialize({' . ((!$user->isGuest)?'id: ' . $user->id . ', displayName: "' . $user->username . '"' : '') . '});',
            CClientScript::POS_HEAD
        );
        
        if($this->isolated) {
            $appMain = 'dev/app.dev.js';
            $this->clientScript->registerScriptFile('/js/libs/backbone-faux-server.js');
        }else{
            $appMain = 'app.js';    
        }
        
        $this->requires['basePath'] = 'application.views.' . $this->controller->id . '.assets.' . $this->controller->action->id . '.js.' . $this->appName;
        
        $fullAppName = ucfirst($this->appName) . 'App';
        
        $this->requires['js'] = array_merge(array($fullAppName . '.js'), $this->requires['js']);
        $this->requires['js'][] = $appMain;

        $this->requires = $this->filterCommonScripts($this->requires);
        
        $this->clientScript->packages = array_merge(
                $this->clientScript->packages, 
                array($this->appName => $this->requires)
        );

        $this->clientScript->registerPackage($this->appName);
        
        foreach ($this->initializers as $index => $initializer) {
            $this->clientScript->registerScript('intlzr' . $index, 
                    'App.addInitializer(function(){ ' . $initializer .' });'
            );
        }
    }
    
    
    protected function filterCommonScripts($requires) {
        $commonPathes = array();
        
        foreach ($requires['js'] as $index => $path) {
            if(preg_match('/^aes:(.*)$/', $path)) {
                list($commonPack, $path) = explode(':', $path);
                $commonPathes[] = $path;
                unset($requires['js'][$index]);
            }
        }
        
        $this->clientScript->addPackage($commonPack, array(
            'baseUrl' => 'js/libs/aes',
            'js' => $commonPathes
        ));
        
        $requires['depends'][] = $commonPack;
        
        return $requires;
    }
}
