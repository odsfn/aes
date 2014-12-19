<?php

class ViewAlbum extends GalleryBaseAction
{
    protected function ownerViewsEmptyList()
    {
        
    }
    
    protected $album;

    protected function proccess($albumType, $galleryItemType)
    {        
        $album_id = Yii::app()->request->getParam('album_id', 0);
        
        $albums = $items = array();
        $nalbums = $nphotos = 0;
        $items_page = Yii::app()->getRequest()->getParam('photos_page', 1);

        $Gallery = Yii::app()->params['Gallery'];

        $model = $albumType::model()->findByPk($album_id);

        // Вывод содержимого альбома
        if ($model) {

            if (!$this->getModule()->canViewAlbum($model)) 
                throw new CHttpException(403);

            $this->album = $model;
            
            $items = $galleryItemType::model()->getRecords(
                'album_id = :album_id', 
                array(
                    ':album_id' => $model->id
                ), 
                $items_page, 
                $Gallery['photos_per_page'], 
                $Gallery['photos_sort']
            );

            $nphotos = $galleryItemType::model()->count('album_id = :album_id', array(':album_id' => $model->id));
        } else
            throw new CHttpException(404);

        if (!$items && $this->getModule()->isOwner($this->user_id, $this->target_id))
            $this->ownerViewsEmptyList();

        // Ajax
        if (Yii::app()->getRequest()->isAjaxRequest) {
            $output = $this->renderPartial($this->viewAlbumAjax, array(
                'model' => $model,
                'photos' => $items,
                'photos_page' => $items_page,
                'photos_per_page' => $Gallery['photos_per_page'],
                'nphotos' => $nphotos,
                'target_id' => $this->target_id,
            ), true);

            Yii::app()->clientScript->renderBodyEnd($output);
            echo $output;
            Yii::app()->end();
        } else {
            return $this->renderPartial($this->viewAlbum, array(
                'model' => $model,
                'nphotos' => $nphotos,
                'photos' => $items,
                'photos_page' => $items_page,
                'photos_per_page' => $Gallery['photos_per_page'],
                'target_id' => $this->target_id,
            ), true);
        }
    }
    
    protected function getMenu()
    {
        $items = $this->getCommonMenuItems();
        $addItem = $items['addItem'];
        $addItem['url']['album_id'] = $this->album->id;
        $menu = array(
            $items['viewAll'],
            array('label' => 'Альбом: ' . $this->album->name, 'url' => '#', 'active' => true),
            $addItem,
            array(
                'label' => Yii::t('album.messages','Редактировать'), 
                'url' => array(
                    $this->getModule()->rootRoute , 
                    'action' => 'UpdateAlbum', 
                    'album_id' => $this->album->id
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
        );
        return $menu;
    }
}

