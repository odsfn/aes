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
     * 
     * To this array will be added several paths automatically.  
     * @var array 
     */
    public $requires = array();
    /**
     * @var CClientScript 
     */
    protected $clientScript;
    
    public function run() {

        $this->clientScript = Yii::app()->clientScript;

        //Registering backbone + marionete
        $this->clientScript->registerPackage('marionette');

        if($this->isolated) {
            $appMain = 'dev/app.dev.js';
            $this->clientScript->registerScriptFile('js/libs/backbone-faux-server.js');
        }else{
            $appMain = 'app.js';    
        }

        $this->requires['basePath'] = 'application.views.' . $this->controller->id . '.assets.' . $this->controller->action->id . '.js.' . $this->appName;
        $this->requires['js'][] = ucfirst($this->appName) . 'App.js';
        $this->requires['js'][] = $appMain;

        $this->clientScript->packages = array_merge(
                $this->clientScript->packages, 
                array($this->appName => $this->requires)
        );

        $this->clientScript->registerPackage($this->appName);
    }
    
}
