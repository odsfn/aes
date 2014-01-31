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
        $model = Election::model()->findByPk($id);
        if (!$model)
            throw new CHttpException('404', 'Page not found');
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

            $model->uploaded_file = CUploadedFile::getInstance($model,'uploaded_file');
            switch ($model->uploaded_file->type) {
                case 'image/jpeg' :
                case 'image/png' :
                case 'image/gif' :
                    $is_image = true;
                    break;
                default:
                    $is_image = false;
            }

            if ($model->validate()) {
                $gallery = new Gallery();
                $gallery->name = true;
                $gallery->description = true;
                $gallery->versions = array(
                    'small' => array(
                        'resize' => array(200, null),
                    ),
                    'medium' => array(
                        'resize' => array(800, null),
                    )
                );

                $transaction = Yii::app()->db->beginTransaction();

                $gallery->save();
                $model->gallery_id = $gallery->id;
                if ($model->save()) {
                    if ($model->uploaded_file && $is_image) {
                        $image = Yii::app()->image->load($model->uploaded_file->tempName);
                        $image->resize(Election::IMAGE_WIDTH, Election::IMAGE_HEIGHT)->quality(Election::IMAGE_QUALITY);
                        $image->save(Yii::app()->basePath . Election::IMAGE_SAVE_PATH.$model->id.'.jpg');
                    }

                    $this->assignRoles($model);

                    $transaction->commit();

                    $this->redirect('/election');
                } else
                    $transaction->rollback();

            }
        }

        $this->render('create',array('model'=>$model));
    }
    
    public function actionAdmins($id) {
        
        $model = Election::model()->findByPk($id);
        if (!$model)
            throw new CHttpException('404', 'Page not found');
        
        $this->layout = '//layouts/election';
        $this->election = $model;
        
        $this->render('admins', array('model'=>$model));
        
    }
    
    
    public function actionCandidates($id) {
        $model = Election::model()->findByPk($id);
        if (!$model)
            throw new CHttpException('404', 'Page not found');
        
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