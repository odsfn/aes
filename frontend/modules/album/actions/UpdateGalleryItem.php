<?php

class UpdateGalleryItem extends GalleryBaseAction
{
    public function run()
    {
        $galleryItemType = $this->albumItemType;
        
        $photo_id = Yii::app()->request->getParam('photo_id');
        $albumContext = Yii::app()->request->getParam('albumContext', false);
        
        $model = $galleryItemType::model()->findByPk($photo_id);
        $user_id = $this->user_id;
        $target_id = $this->target_id;
        
        if (!$model)
            throw new CHttpException(404);
        
        $target_id = $model->target_id;
        
        if (!$user_id || !$this->getModule()->isOwner($user_id, $target_id))
            throw new CHttpException(403);

        $attributes = Yii::app()->request->getPost($galleryItemType);

        if (isset($attributes['album_id']) 
            && $attributes['album_id'] == 'NULL' || $attributes['album_id'] == '0' || $attributes['album_id'] == ''
        ){
            $attributes['album_id'] = null;
        }
        
        $model->attributes = $attributes;
        
        if($model->isNewRecord)
            $updated = false;
        else
            $updated = true;
        
        if ($model->save()) {
            
            $canEdit = false;
            if ($model->user_id == $user_id)
                $canEdit = true;
            
            if ($updated)
                $updated = Yii::app()->locale->dateFormatter->formatDateTime(time(), null, 'short');
                    
            $response = array(
                'success' => true,
                'html' => $this->renderPartial($this->viewGalleryItemDetailsPanel, array(
                    'model' => $model,
                    'canEdit' => $canEdit,
                    'albumContext' => $albumContext,
                    'updated' => $updated
                ), true)
            );
        } else {
            
            $response = array(
                'success' => false,
                'html' => $this->renderPartial($this->viewUpdateGalleryItem, array('model' => $model), true)
            );
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
}

