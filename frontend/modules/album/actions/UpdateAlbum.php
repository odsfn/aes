<?php

class UpdateAlbum extends GalleryBaseAction
{
    public function run()
    {
        $albumType = $this->albumType;
        $albumItemType = $this->albumItemType;
        
        $album_id = Yii::app()->request->getParam('album_id', 0);
        
        $model = $albumType::model()->findByPk($album_id);
                
        if (!$model)
            throw new CHttpException(404);

        // add access check handler method defined as attribute of AlbumModule
        if (!$this->user_id || !$this->getModule()->canEditAlbum($model))
            throw new CHttpException(403);

        if ($attributes = Yii::app()->request->getPost($albumType)) {
            $model->setScenario('update');
            $model->attributes = $attributes;
            if ($model->save()) {
                $items = $albumItemType::model()->updateAll(
                    array('permission' => $model->permission), 
                    'album_id = :album_id', 
                    array(':album_id' => $model->id)
                );
                Yii::app()->user->setFlash('success', 'Изменения сохранены');
                $this->redirect(array($this->getModule()->rootRoute , 'action' => 'ViewAlbum', 'album_id' => $model->id));
            }
        }

        $menu = array(
            array('label' => 'Все ' . $this->pluralLabel, 'url' => array($this->getModule()->rootRoute)),
            array(
                'label' => 'Альбом: ' . $model->name, 
                'url' => array(
                    $this->getModule()->rootRoute, 
                    'action' => 'ViewAlbum', 
                    'album_id' => $model->id
                )
            ),
            array(
                'label' => 'Добавить ' . $this->singularLabel, 
                'url' => array(
                    $this->getModule()->rootRoute , 
                    'action' => 'CreateGalleryItem', 
                    'album_id' => $model->id
                ), 
                'visible' => $this->getModule()->canAddPhotoToAlbum($model)
            ),
            array('label' => 'Редактировать', 'url' => '#', 'active' => true),
        );

        $content = $this->renderPartial($this->viewCreateAlbum, array('model' => $model), true);
        
        $this->renderPartial($this->viewContent, array('content' => $content, 'menu' => $menu, 'target_id' => $this->target_id));
    }
}
