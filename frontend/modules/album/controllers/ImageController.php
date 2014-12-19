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
        
        $target_id = $model->target_id;
        $user_id = Yii::app()->user->id;
        
        if (!$user_id || !$this->getModule()->isOwner($user_id, $target_id))
            throw new CHttpException(403);
        
        $this->getModule()->getComponent('image')->rotate($file_path = $model->getAbsolutePath(), $direction);
        $this->getModule()->createThumbnails($file_path, true);
        
        echo CJSON::encode(array('success'=>true));
    }
}
