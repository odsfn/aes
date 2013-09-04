<?php
class ApiModule extends CWebModule {
    
    public function init() {
        
        $this->setAliases(array(
            'ext'
        ));
        
        $this->setImport(array(
            'common.extensions.restfullyii.components.*',
            'api.components.*'
        ));
    }
    
}