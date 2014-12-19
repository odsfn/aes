<?php

class DeleteAlbum extends GalleryBaseAction
{
    public function run()
    {
        $album_id = Yii::app()->request->getParam('album_id', 0);
        $albumType = $this->albumType;
        
        $model = $albumType::model()->findByPk($album_id);
                
        if (!$this->user_id || !$this->getModule()->canDeleteAlbum($model))
            throw new CHttpException(403);

        if ($model->delete()) {
            Yii::app()->user->setFlash('success', Yii::t('album.messages','Альбом удален успешно'));
            $this->redirect(array($this->getModule()->rootRoute));
        }
    }
}

