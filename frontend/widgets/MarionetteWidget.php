<?php
/**
 * Base class for Backbone.Marionette widgets. It accembles all common client 
 * scripts and packages, generates code to initialization of commonly used objects
 * on client and publishes widgets' templates and css.
 * 
 * By demand can generate code for widget initialization and showing.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class MarionetteWidget extends CWidget {
    
    public $widgetName;
    
    public $_basePath;

    /**
     * Flag that indicates whether to run application in isolated mode. If true
     * all server requests will be collected by fake server object on client side
     * @var boolean 
     */
    public $isolated = false;

    /**
     * List of the required files. See the Yii Packages configuration format.
     * You also can specify aes:...some_path... to load some common collections
     * models or other components
     * 
     * To this array will be added several paths automatically.  
     * @var array 
     */
    public $requires = array();

    public $show = false;
    
    public $jsConstructorOptions = array();
    
    /**
     * @var CClientScript 
     */
    protected $_clientScript;
    
    protected static $registered = false;


    public function run() {

        if(!self::$registered) {
            
            $this->registerCommon();

            $this->registerSelf();

            echo file_get_contents(Yii::getPathOfAlias($this->basePath) . '/templates.html');
            
            self::$registered = true;
            
        }
        
        if($this->show) {
            
            if(isset($this->show['el'])) {  //Should be showen in specified element selector
                $elSelector = $this->show['el'];
            }else{                          //Prepare the tag for it
                
                $elSelector = '#' . $this->id;
                
                list($tag, $htmlOptions) = $this->show;
                
                if(!isset($htmlOptions['id']))
                    $htmlOptions['id'] = $this->id;
                else{
                    $elSelector = '#' . $htmlOptions['id'];
                }
                
                echo CHtml::tag($tag, $htmlOptions, '');
            }
            
            // Register instantiation js code
            $varName = $this->id . '_' . $this->widgetId . 'Widget';
            
            $jsonOpts = json_encode($this->jsConstructorOptions, JSON_FORCE_OBJECT);
            
            $this->clientScript->registerScript('init' . $this->widgetName .  $this->id, "
                var $varName = {$this->widgetName}.create($jsonOpts);

                $('$elSelector').html($varName.render().el);
                $varName.triggerMethod('show');
            ", CClientScript::POS_READY);       
        }
    }
    
    public function getWidgetId() {
        return lcfirst(str_replace('Widget', '', $this->widgetName));
    }
    
    public function getBasePath() {
        if(!$this->_basePath) {
            $this->_basePath = 'frontend.www.js.libs.aes.widgets.' . $this->widgetId;
        }
        
        return $this->_basePath;
    }
    
    /**
     * @param string $path Path alias to the widget folder
     */
    public function setBasePath($path) {
        $this->_basePath = $path;
    }
    
    protected function getClientScript() {
        if(!$this->_clientScript) {
            $this->_clientScript = Yii::app()->clientScript;
        }
        
        return $this->_clientScript;
    }
    
    protected function registerCommon() {
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
    }
    
    protected function registerSelf() {
        $this->requires['baseUrl'] = 'js/libs/aes/widgets/' . $this->widgetId;
        
        if(!isset($this->requires['js'])) {
            $this->requires['js'] = array();
        }
        $this->requires['js'] = array_merge($this->requires['js'], array($this->widgetName . '.js'));
         
        $this->requires = $this->filterCommonScripts($this->requires);
        
        if(!isset($this->requires['css'])) {
            $this->requires['css'] = array();
        }
        $this->requires['css'] = array_merge($this->requires['css'], array($this->widgetId . '.css'));
        
        $this->clientScript->packages = array_merge(
                $this->clientScript->packages, 
                array($this->widgetName => $this->requires)
        );

        $this->clientScript->registerPackage($this->widgetName);        
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
