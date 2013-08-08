<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserPageController extends EController {

    public $profile;
    /**
     * Authenticated user's page
     */
    public function actionIndex() {
	
	$user = Yii::app()->user;
	
	if($user->isGuest) 
	    $this->redirect ('/');
	
	$this->profile = $user->profile;
	
	$this->publishResources();
	$this->render('userPage', array('profile' => $this->profile));
    }
    
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
}
