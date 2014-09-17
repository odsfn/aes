<?php
/** 
 * Controller for superadmin actions like voter group management, system settings
 */
class AdminController extends FrontController 
{
    public $layout = '//layouts/column1';


    public function filters()
    {
        return array(
            'accessControl',
        );
    }
    
    public function accessRules()
    {
        return array(
            array('allow',
                'roles'=>array('superadmin')
            ),
            array('deny',
                'users'=>array('*')
            )
        );
    }

    public function actionVoterGroups()
    {
        $this->render('voterGroups');
    }
}
