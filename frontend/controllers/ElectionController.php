<?php

class ElectionController extends FrontController
{   
    public $election;
    
    public function init() {
        $this->attachBehavior('breadcrumbs', new CrumbsBehaviour);
        $this->breadcrumbs->setEnabled(true);
        parent::init();
    }    
    
    public function filters(){
        return array(
            'accessControl',
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('create'),
                'users'=>array('@')
            ),
            array('deny',
                'actions'=>array('create'),
                'users'=>array('*')
            )
        );
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionView($id)
    {
        $model = $this->getModel($id);
        
        $this->layout = '//layouts/election';
        $this->election = $model;
        $this->render('view', array('model'=>$model));
    }

    public function actionCreate()
    {
        $model=new Election;

        if (isset($_POST['Election'])) {

            $model->attributes = $_POST['Election'];
            $model->user_id = Yii::app()->user->id;
            $model->status = 0;

            if ($model->validate()) {
                $transaction = Yii::app()->db->beginTransaction();

                if ($model->save()) {
                    $this->assignRoles($model);

                    $transaction->commit();

                    Yii::app()->user->setFlash('success', Yii::t('aes', 'Election created'));
                    $this->redirect(array('/election/view/' . $model->id));
                } else
                    $transaction->rollback();

            }
        }

        $this->render('create',array('model'=>$model));
    }
    
    public function actionManagement($id) {
        $model = $this->getModel($id);
        $this->layout = '//layouts/election';
        $this->election = $model;
        
        if(!Yii::app()->user->checkAccess('election_administration', array('election' => $this->election)))
            throw new CHttpException(403, 'You have no rights to perform this action');
        
        if (isset($_POST['Election'])) {

            $model->attributes = $_POST['Election'];

            if($model->save()) {
                Yii::app()->user->setFlash('success', Yii::t('aes', 'Changes saved'));
                $this->redirect(array('/election/management/' . $model->id));
            }
            
        }
        
        $this->render('management', array('model'=>$model));
    }
    
    public function actionProvisions($id) {
        $model = $this->getModel($id);
        $this->layout = '//layouts/election';
        $this->election = $model;
        
        $canManage = Yii::app()->user->checkAccess('election_administration', array('election' => $this->election));
        
        $this->render('provisions', array('model'=>$model, 'canManage' => $canManage));
    }
    
    public function actionAdmins($id) {
        
        $model = $this->getModel($id);
        
        $this->layout = '//layouts/election';
        $this->election = $model;
        
        $this->render('admins', array('model'=>$model));
        
    }
     
    public function actionCandidates($id) {
        $model = $this->getModel($id);
        
        $this->layout = '//layouts/election';
        $this->election = $model;
        
        $candidate = false;
        
        if(Yii::app()->user->id) {
            $candidate = Candidate::model()->findByAttributes(array(
                'user_id'    => Yii::app()->user->id,
                'election_id'=> $model->id
            ));
        }
        
        $this->render('candidates', array('model'=>$model, 'candidate'=>$candidate));        
    }
    
    public function actionElectorate($id) {
        $model = $this->getModel($id);
        
        $this->layout = '//layouts/election';
        $this->election = $model;
        
        $this->render('electorate', array('model'=>$model));
    }
    
    public function actionManageVotersGroups($id)
    {
        $model = $this->getModel($id);
        $this->layout = '//layouts/election';
        $this->election = $model;
        
        if(!Yii::app()->user->checkAccess('election_administration', array('election' => $this->election)))
            throw new CHttpException(403, 'You have no rights to perform this action');
        
        $this->render('manageVotersGroups', array('model'=>$model));    
    }

    public function actionRegisterElectorsFromGroups()
    {
        $election_id = (int)$_POST['election_id'];
        $model = $this->getModel($election_id);
        
        if(!Yii::app()->user->checkAccess('election_administration', array('election' => $model)))
            throw new CHttpException(403, 'You have no rights to perform this action');
        
        $registration = new VoterGroupMembersRegistration($model);
        $registration->run();
        
        $this->renderJson(array(
            'success'=> true,
            'message'=>'Operation finished successfully'
        ));
    }

    public function actionPhotos($id)
    {
        $electionId= $_GET['id'];
        $this->election = $election = $this->getModel($electionId);
        $this->layout = '//layouts/election';
        
        Yii::app()->getModule('album')->rootRoute = '/election/photos/' . $electionId;
        
        $widgetOut = $this->widget('album.widgets.Gallery', array(
            'type' => 'image',
            'target_id' => $election->target_id,
        ), true);
        
        $this->render('photos', array(
            'galleryWidgetOutput' => $widgetOut
        ));
    }    
    
    public function actionVideos()
    {   
        $electionId= $_GET['id'];
        $this->election = $election = $this->getModel($electionId);
        $this->layout = '//layouts/election';
        
        Yii::app()->getModule('album')->rootRoute = '/election/videos/' . $electionId;
        
        $widgetOut = $this->widget('album.widgets.Gallery', array(
            'type' => 'video',
            'target_id' => $election->target_id,
        ), true);
        
        $this->render('videos', array(
            'galleryWidgetOutput' => $widgetOut
        ));
    }    
    
    protected function getModel($id) {
        $model = Election::model()->findByPk($id);
        
        if (!$model)
            throw new CHttpException('404', 'Page not found');
        
        return $model;
    }
    
    /**
     * Assigns roles on creatrion of Election
     * 
     * @param Election $model
     */
    protected function assignRoles($model) {
        
        $model->assignRoleToUser($model->user_id, 'election_creator');
        
        //to assign admins use
        //$model->assignRoleToUser($user_id, 'election_admin');
        
        //to assign commentModerators user
        //$model->assignRoleToUser($user_id, 'election_commentModerator');
    }
}