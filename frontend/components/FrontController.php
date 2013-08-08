<?php
/*
 * FrontController encapsulates common logic for front controllers. Such as 
 * resources publishing.
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class FrontController extends EController {
    /**
     * Publishes specific resources for current controller/action
     */
    protected function publishResources() {
	
	$pathes = array();
	
	$basePath = Yii::getPathOfAlias('application.views.' . $this->id . '.assets') . '/' . $this->action->id;
	
	$pathes = array(
	    $basePath . '.css',
	    $basePath . '.js',
	);
	
	//Does directory present
	//@TODO: 
	// 1. Publishing childs of the directory
	// 2. Caching for production
//	if(file_exists($basePath . '/')) {
//	    
//	}
	
	foreach ($pathes as $path) {
	    if(file_exists($path)) {	
		Yii::app()->clientScript->registerCssFile(
		    Yii::app()->assetManager->publish($path)
		);
	    }
	}
    }
    
    public function render($view, $data = null, $return = false) {
	$this->publishResources();
	parent::render($view, $data, $return);
    }
}