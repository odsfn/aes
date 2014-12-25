<?php
/**
 * Image manipulation controller
 */
class ImageController extends CController
{
    public function actionRotateImage($gitem_id, $direction)
    {
        $model = File::model()->findByPk($gitem_id);

        if (!$model)
            throw new CHttpException(404);
        
        if (!Yii::app()->user->checkAccess('album_editGItem', array('item' => $model)))
            throw new CHttpException(403);
        
        $this->getModule()->getComponent('image')->rotate($file_path = $model->getAbsolutePath(), $direction);
        $this->getModule()->createThumbnails($file_path, true);
        
        echo CJSON::encode(array('success'=>true));
    }
}
