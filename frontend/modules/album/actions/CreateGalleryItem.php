<?php

class CreateGalleryItem extends GalleryBaseAction
{    
    protected $album;
    
    protected function proccess($albumType, $galleryItemType)
    {
        if (!$this->user_id)
            throw new CHttpException(403);

        $album_params = array();
        
        $album_id = Yii::app()->request->getParam('album_id');
        
        if ($album_id) {
            $parentAlbum = $albumType::model()->findByPk($album_id);
            if ($parentAlbum) {
                if(!$this->getModule()->canAddItemToAlbum($parentAlbum, $this->user_id))
                    throw new CHttpException(403);

                $this->album = $parentAlbum;
            } else
                throw new CHttpException(500);
        } else {
            if(!Yii::app()->user->checkAccess('album_createGItem', array('targetId' => $this->target_id)))
                throw new CHttpException(403);
        }

        $item = new $galleryItemType();
        
        if(!$item->album_id && $album_id)
            $item->album_id = $album_id;
        
        if ($attrs = Yii::app()->request->getPost($galleryItemType)) {
            
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
        } else {
            $item->target_id = $this->target_id;
        }
        
        return $this->renderPartial(
            $this->viewCreateGalleryItem, 
            array(
                'model' => $item,
                'target_id' => $this->target_id
            ), 
            true
        );
    }
    
    protected function getMenu()
    {
        $menuItems = $this->getCommonMenuItems();
        
        $addItem = $menuItems['addItem'];
        $addItem['active'] = true;
        
        $menu = array(
            $menuItems['viewAll'],
            $addItem,
            $menuItems['addAlbum']
        );
        
        if($this->album) {
            array_splice($menu, 2, 1, array(
                array(
                    'label' => 'Редактировать', 
                    'url' => array(
                        $this->getModule()->rootRoute , 
                        'action' => 'UpdateGalleryItem', 
                        'album_id' => $this->album->id
                    ), 
                    'visible' => $this->getModule()->canEditAlbum($this->album)
                )
            ));
            
            array_splice($menu, 1, 0, array(
                array(
                    'label' => 'Альбом: ' . $this->album->name, 
                    'url' => array(
                        $this->getModule()->rootRoute , 
                        'action' => 'ViewAlbum', 
                        'album_id' => $this->album->id
                    )
                )
            ));
        }
        
        return $menu;
    }
}

