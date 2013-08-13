<?php
/*
 * This widget assembles specified client application
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
     * List of the required .js files 
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

        $appMain = ($this->isolated) ? 'dev/app.dev.js' : 'app.js';

        $this->requires['basePath'] = 'application.views.' . $this->controller->id . '.assets.' . $this->controller->action->id . '.js.' . $this->appName;
        $this->requires['js'][] = $appMain;

        $this->clientScript->packages = array_merge(
                $this->clientScript->packages, 
                array($this->appName => $this->requires)
        );

        $this->clientScript->registerPackage($this->appName);
    }
    
}
