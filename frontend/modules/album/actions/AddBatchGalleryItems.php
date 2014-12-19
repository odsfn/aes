<?php

/* 
 * Controls page where multiple Files can be uploaded and edited
 */
class AddBatchGalleryItems extends GalleryBaseAction
{
    protected $album;
    
    protected function proccess($albumType, $galleryItemType)
    {
        $this->fixSession();
        
        $user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);
        if (!$user_id)
            throw new CHttpException(403);

        $album_params = $photo_params = array();
        $album_id = Yii::app()->request->getParam('album_id', 0);
        
        $uploaderParams = array(
            'action' => 'AddBatchGalleryItems'
        );
        
        if ($album_id) {
            $model = $albumType::model()->findByPk($album_id);
            if ($model) {
                $this->album = $model;
                if(!$this->getModule()->canAddItemToAlbum($model, $user_id))
                    throw new CHttpException(403);

                $uploaderParams = array_merge($uploaderParams, 
                    array(
                        'album_id' => $model->id, 
                        'target_id' => $this->target_id
                    )
                );
            } else
                throw new CHttpException(500);
        } else {
            if(!$this->getModule()->isOwner($user_id, $this->target_id))
                throw new CHttpException(403);
        }

        $photo_params['uploader'] = $this->getController()->createUrl(
            $this->getModule()->rootRoute, 
            $uploaderParams
        );
        
        $photo = new $galleryItemType();
        if ($file_name = Yii::app()->request->getPost('Filename')) {
            $photo->setScenario('upload');
            $file_temp_path = CUploadedFile::getInstance($photo, 'filename')->tempName;
            $file_path = $this->getModule()->getComponent('image')->createImage($file_temp_path, $file_name);

            $this->getModule()->createThumbnails($file_path);

            if (!$file_path)
                throw new CHttpException(500);
            if (isset($model))
                $permission = $albumType::model()->findByPk($model->id)->permission;
            else
                $permission = 0;

            $file_path = str_replace(Yii::getPathOfAlias('webroot') . DIRECTORY_SEPARATOR, '', $file_path);

            $photo->attributes = array(
                'filename' => basename($file_path),
                'target_id' => $this->target_id,
                'album_id' => empty($album_id) ? null : $album_id,
                'path' => $file_path,
                'permission' => $permission,
            );
            if ($photo->save()) {                        
                echo CJSON::encode(array(
                    'success' => true,
                    'html' => $this->renderPartial(
                        'album.views.base._image_update_compact', 
                        array('model'=>$photo),
                        true
                    )
                ));
                Yii::app()->end();
            }
        }

        $photo_params['photo'] = $photo;        
        return $this->renderPartial('album.views.base._images_upload', $photo_params, true);
    }
    
    protected function getMenu()
    {
        $menu = parent::getMenu();
        
        if($this->album) {
            array_splice($menu, 2, 1, array(
                array(
                    'label' => 'Редактировать', 
                    'url' => array(
                        $this->getModule()->rootRoute , 
                        'action' => 'UpdateGalleryItem', 
                        'album_id' => $this->album->id
                    ), 
                    'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
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
    
    protected function fixSession()
    {
        $request = Yii::app()->request;
        
        if (isset($_POST['SESSION_ID']) 
            && Yii::app()->request->getPost('Filename')
        ) {
            $session=Yii::app()->getSession();
            $actualSession = $_POST['SESSION_ID'];
            
            if ($session->sessionID != $actualSession) {
                $session->close();
                $session->sessionID = $_POST['SESSION_ID'];
                $session->open();
            }
        }
    }
}
