<?php

class ElectionController extends FrontController
{

    public $breadcrumbs=array();

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

            if ($model->save()) {
                if ($model->uploaded_file && $is_image) {
                    $image = Yii::app()->image->load($model->uploaded_file->tempName);
                    $image->resize(Election::IMAGE_WIDTH, Election::IMAGE_HEIGHT)->quality(Election::IMAGE_QUALITY);
                    $image->save(Yii::app()->basePath . Election::IMAGE_SAVE_PATH.$model->id.'.jpg');
                }
                
                //$this->assignRoles($model);
                
                $this->redirect('/election');
            }
        }

        $this->render('create',array('model'=>$model));
    }
    
    /**
     * Assigns roles on creatrion of Election
     * 
     * @param Election $model
     */
    protected function assignRoles($model) {
        
        $auth = Yii::app()->authManager;
        
        $role = $auth->createRole(($uniqRoleName = 'Election_' . $model->id . '_commentModerator'));
        $role->addChild('commentModerator');
        
        //Allow to the creator of election moderate comments
        Yii::app()->authManager->assign($uniqRoleName, $model->user_id, 
                'return ($data[\'targetType\']==$params[\'targetType\'] && $data[\'targetId\']==$params[\'targetId\']);',
                array(
                    'targetType' => 'Election',
                    'targetId'   => $model->id
                    )
        );
        
    }
}