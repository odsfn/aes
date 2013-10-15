<?php

class ElectionController extends FrontController
{

    public function actionIndex()
    {
        $this->render('index');
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
                $this->redirect('/election');
            }
        }

        $this->render('create',array('model'=>$model));
    }
}