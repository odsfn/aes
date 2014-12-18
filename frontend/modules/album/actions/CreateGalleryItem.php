<?php

class CreateGalleryItem extends GalleryBaseAction
{
    public function run()
    {
        if (!$this->user_id)
            throw new CHttpException(403);

        $album_params = array();
        
        $album_id = Yii::app()->request->getParam('album_id');
        
        $albumType = $this->albumType;
        $albumItemType = $this->albumItemType;
        
        if ($album_id) {
            $parentAlbum = $albumType::model()->findByPk($album_id);
            if ($parentAlbum) {

                if(!$this->getModule()->canAddPhotoToAlbum($parentAlbum, $this->user_id))
                    throw new CHttpException(403);

                $menu = array(
                    array('label' => 'Все ' . $this->pluralLabel, 'url' => array($this->getModule()->rootRoute )),
                    array(
                        'label' => 'Альбом: ' . $parentAlbum->name, 
                        'url' => array(
                            $this->getModule()->rootRoute , 
                            'action' => 'ViewAlbum', 
                            'album_id' => $parentAlbum->id
                        )
                    ),
                    array(
                        'label' => 'Добавить ' . $this->singularLabel, 'url' => '#', 
                        'active' => true, 
                        'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
                    ),
                    array(
                        'label' => 'Редактировать', 
                        'url' => array(
                            $this->getModule()->rootRoute , 
                            'action' => 'UpdateGalleryItem', 
                            'album_id' => $parentAlbum->id
                        ), 
                        'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
                    )
                );
            } else
                throw new CHttpException(500);
        } else {
            if(!$this->getModule()->isOwner($this->user_id, $this->target_id))
                throw new CHttpException(403);

            $menu = array(
                array(
                    'label' => 'Все ' . $this->pluralLabel, 
                    'url' => array($this->getModule()->rootRoute)
                ),
                array(
                    'label' => 'Добавить ' . $this->singularLabel, 'url' => '#', 'active' => true, 
                    'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
                ),
                array(
                    'label' => 'Создать альбом', 
                    'url' => array(
                        $this->getModule()->rootRoute , 
                        'action' => 'CreateAlbum'
                    ), 
                    'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
                ),
            );
        }

        $item = new $albumItemType();
        
        if(!$item->album_id && $album_id)
            $item->album_id = $album_id;
        
        if ($attrs = Yii::app()->request->getPost($albumItemType)) {
            
            $attrs = array_merge($attrs, array(
                'target_id' => $this->target_id
            ));
            
            if (isset($parentAlbum))
                $attrs['permission'] = $parentAlbum->permission;
            
            $item->attributes = $attrs;
            
            if ($item->save()) {                        
                Yii::app()->user->setFlash('success', 'Запись создана успешно');
                $url = array(
                    $this->getModule()->rootRoute,
                );
                
                if ($album_id) {
                    $url['action'] = 'ViewAlbum';
                    $url['album_id'] = $album_id;
                }
                
                $this->redirect($url);
            }
        }
        
        $content = $this->renderPartial(
            $this->viewCreateGalleryItem, 
            array(
                'model' => $item,
                'target_id' => $this->target_id
            ), 
            true
        );

        $this->renderPartial($this->viewContent, array('content' => $content, 'menu' => $menu));
    }
}

