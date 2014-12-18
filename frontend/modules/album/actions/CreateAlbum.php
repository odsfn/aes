<?php

class CreateAlbum extends GalleryBaseAction
{
    public function run()
    {
        if (!$this->user_id || !$this->getModule()->canCreateAlbum($this->target_id, $this->user_id))
            throw new CHttpException(403);

        $albumClass = $this->albumType;
        
        $model = new $albumClass;
        if ($attributes = Yii::app()->request->getPost($albumClass)) {
            $model->setScenario('create');
            $model->attributes = $attributes;
            $model->target_id = $this->target_id;
            if ($model->save()) {
                $this->redirect(array($this->getModule()->rootRoute , 'action' => 'ViewAlbum', 'album_id' => $model->id, 'target_id' => $this->target_id));
            }
        }

        $menu = array(
            array('label' => Yii::t('album.messages', 'Все ' . $this->pluralLabel), 'url' => array( $this->getModule()->rootRoute )),
            array('label' => Yii::t('album.messages', 'Новый альбом'), 'url' => '#', 'active' => true),
        );

        $content = $this->getController()->renderPartial($this->viewCreateAlbum, array('model' => $model), true);
               
        $this->getController()->renderPartial($this->viewContent, array('content' => $content, 'menu' => $menu, 'target_id' => $this->target_id));
    }
}

