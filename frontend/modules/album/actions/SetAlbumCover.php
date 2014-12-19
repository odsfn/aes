<?php

class SetAlbumCover extends GalleryBaseAction
{
    public function run()
    {
        $galleryItemType = $this->albumItemType;
        $item_id = Yii::app()->request->getParam('gitem_id');
        
        $model = $galleryItemType::model()->with('album')->findByPk($item_id);
        
        if (!$this->user_id)
            throw new CHttpException(403);

        if (isset($model->album)) {
            $model->album->cover_id = $model->id;
            $model->album->save();
        }

        if (Yii::app()->getRequest()->isAjaxRequest) {
            $this->renderPartial($this->viewItemAlbumCoverMark);
            Yii::app()->end();
        } else {
            $this->redirect(array(
                $this->getModule()->rootRoute, 
                'action' => 'ViewGalleryItem', 
                'gitem_id' => $item_id,
                'albumContext' => $model->album_id
            ));
        }
    }
}

