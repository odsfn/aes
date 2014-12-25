<?php

class CreateAlbum extends GalleryBaseAction
{    
    public function proccess($albumType, $galleryItemType)
    {
        if (!$this->user_id || !$this->getModule()->canCreateAlbum($this->target_id, $this->user_id))
            throw new CHttpException(403);
                
        $model = new $albumType;
        if ($attributes = Yii::app()->request->getPost($albumType)) {
            $model->setScenario('create');
            $model->attributes = $attributes;
            $model->target_id = $this->target_id;
            if ($model->save()) {
                $this->redirect(array($this->getModule()->rootRoute , 'action' => 'ViewAlbum', 'album_id' => $model->id, 'target_id' => $this->target_id));
            }
        }

        return $this->getController()->renderPartial($this->viewCreateAlbum, array('model' => $model), true);
    }

    protected function getMenu()
    {
        $menuItems = $this->getCommonMenuItems();
        return array(
            $menuItems['viewAll'],
            array(
                'label' => Yii::t('album.messages', 'Новый альбом'), 
                'url' => '#', 
                'active' => true
            ),
        );
    }
}

