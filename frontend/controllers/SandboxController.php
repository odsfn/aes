<?php
/**
 * This is controller for testing and playing with isolated components of view layer
 * 
 * @TODO: Move it to frontend/tests/sandbox with all related views
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class SandboxController extends EController {
    
    public $layout = '//layouts/empty';
    
    public function actionPlay() {
        
        if(!defined('TEST_APP_INSTANCE') || !TEST_APP_INSTANCE)
            throw new CHttpException(404);

        $this->render($_GET['view']);
    }
    
}
