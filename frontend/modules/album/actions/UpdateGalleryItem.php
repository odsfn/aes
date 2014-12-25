<?php

class UpdateGalleryItem extends GalleryBaseAction
{
    public function run()
    {
        $galleryItemType = $this->albumItemType;
        
        $gitem_id = Yii::app()->request->getParam('gitem_id');
        $albumContext = Yii::app()->request->getParam('albumContext', false);
        
        $model = $galleryItemType::model()->findByPk($gitem_id);
        $user_id = $this->user_id;
        
        if (!$model)
            throw new CHttpException(404);
        
        if (!Yii::app()->user->checkAccess('album_editGItem', array('item' => $model)))
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
            
            $formView = $this->viewGalleryItemDetailsPanel;
            
            if (Yii::app()->request->getParam('form_type') == 'compact')
                $formView = '_image_update_compact';
            
            $response = array(
                'success' => true,
                'html' => $this->renderPartial($formView, array(
                    'model' => $model,
                    'canEdit' => $canEdit,
                    'albumContext' => $albumContext,
                    'updated' => $updated
                ), true)
            );
        } else {
            
            $formView = $this->viewUpdateGalleryItem;
            
            if (Yii::app()->request->getParam('form_type') == 'compact')
                $formView = '_image_update_compact';
            
            $response = array(
                'success' => false,
                'html' => $this->renderPartial($formView, array('model' => $model), true)
            );
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
}

