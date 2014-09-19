<?php
/** 
 * Controller for superadmin actions like voter group management, system settings
 */
class AdminController extends FrontController 
{
    public $layout = '//layouts/fullwidth';

    public function init() {
        $this->attachBehavior('breadcrumbs', new CrumbsBehaviour);
        $this->breadcrumbs->setEnabled(true);
        
        parent::init();
    }
    
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
    
    public function actionCopyGroup()
    {
        $id = (int)$_POST['id'];
        $group = VoterGroup::model()->findByPk($id);
        
        if (!$group) {
            $this->renderJson(array(
                'success'=> false,
                'message'=>'Group with id "' . $id . '" was not found'
            ));
            return;
        }
        
        $newGroup = $group->copy();
        
        if (!$newGroup) {
            $this->renderJson(array(
                'success' => false,
                'message' => 'Copying failed'
            ));
            return;
        }
        
        $this->renderJson(array(
            'success' => true,
            'message' => 'Group copied successfully',
            'data' => array('id' => $newGroup->id )
        ));
    }
}
