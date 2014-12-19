<?php

class DeleteGalleryItem extends GalleryBaseAction
{
    public function run()
    {
        $galleryItemType = $this->albumItemType;
        $item_id = Yii::app()->request->getParam('photo_id');
        
        $model = $galleryItemType::model()->findByPk($item_id);

        if (!$model)
            throw new CHttpException(404);
        
        $target_id = $model->target_id;
        $user_id = Yii::app()->user->id;
        
        if (!$user_id || !$this->getModule()->isOwner($user_id, $target_id))
            throw new CHttpException(403);

        $afterDeleteHandler = function($event) {
            $originalPath = $event->sender->path;
            $filesPathes = Yii::app()->getModule('album')->getAbsolutePathes($originalPath);

            foreach($filesPathes as $path) {
                if(file_exists($path)) unlink($path);
            }
        };
        $model->attachEventHandler('onAfterDelete', $afterDeleteHandler);

        if (!$model->delete()) 
            throw new CException('Photo #'.$item_id.' deletion failed');

        if (Yii::app()->request->isAjaxRequest) {
            
            echo CJSON::encode(array(
                'success'=>true,
                'html'=>Yii::t('album.messages', 'Элемент был удален успешно')
            ));
            
            Yii::app()->end();
        }
        
        $this->redirect(array($this->getModule()->rootRoute , 'target_id' => $target_id));
    }
}

