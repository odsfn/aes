<?php

class UpdateAlbum extends GalleryBaseAction
{    
    protected $album;
    
    protected function proccess()
    {
        $albumType = $this->albumType;
        $albumItemType = $this->albumItemType;
        
        $album_id = Yii::app()->request->getParam('album_id', 0);
        
        $model = $albumType::model()->findByPk($album_id);
                
        if (!$model)
            throw new CHttpException(404);

        $this->album = $model;
        
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

        return $this->renderPartial($this->viewCreateAlbum, array('model' => $model), true);
    }
    
    protected function getMenu()
    {
        $items = $this->getCommonMenuItems();
        $addItem = $items['addItem'];
        $addItem['url']['album_id'] = $this->album->id;
        $menu = array(
            $items['viewAll'],
            array(
                'label' => 'Альбом: ' . $this->album->name, 
                'url' => array(
                    $this->getModule()->rootRoute, 
                    'action' => 'ViewAlbum', 
                    'album_id' => $this->album->id
                )
            ),
            $addItem,
            $items['editing']
        );
        
        return $menu;
    }
}
