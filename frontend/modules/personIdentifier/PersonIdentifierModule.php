<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PersonIdentifierModule extends CWebModule
{
    public $personIdentifiers = array();

    public $defaultIdentifierType = '';
    
    public $photoExtensions = array('png', 'jpg');

    public $photoMaxSize = 3000000;    
    
    public $imagesDir = '/uploads/person_identifiers';    
    
    public $identifierExamplesPath = '/uploads/person_identifiers/examples';
    
    public $identifierExampleMaxWidth = 400;
    
    public $identifierExampleMaxHeight = 500;
    
    /**
     * Path alias to custom identifiers
     * @var string
     */
    public $customIdentifiersPath = 'personIdentifier.identifiers';

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'personIdentifier.models.*',
            'personIdentifier.widgets.*'
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
