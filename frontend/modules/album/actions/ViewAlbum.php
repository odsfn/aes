<?php

class ViewAlbum extends GalleryBaseAction
{
    protected function ownerViewsEmptyList()
    {
        
    }
    
    public function run()
    {
        $albumItemType = $this->albumItemType;
        $albumType = $this->albumType;
        
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

            $items = $albumItemType::model()->getRecords(
                'album_id = :album_id', 
                array(
                    ':album_id' => $model->id
                ), 
                $items_page, 
                $Gallery['photos_per_page'], 
                $Gallery['photos_sort']
            );

            $nphotos = $albumItemType::model()->count('album_id = :album_id', array(':album_id' => $model->id));
        } else
            throw new CHttpException(404);

        if (!$items && $this->getModule()->isOwner($this->user_id, $this->target_id))
            $this->ownerViewsEmptyList();

        $menu = array(
            array('label' => 'Все ' . $this->pluralLabel, 'url' => array($this->getModule()->rootRoute)),
            array('label' => 'Альбом: ' . $model->name, 'url' => '#', 'active' => true),
            array(
                'label' => 'Добавить ' . $this->singularLabel, 'url' => array(
                    $this->getModule()->rootRoute , 
                    'action' => 'CreateGalleryItem', 'album_id' => $model->id
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
            array(
                'label' => 'Редактировать', 'url' => array(
                    $this->getModule()->rootRoute , 
                    'action' => 'UpdateAlbum', 
                    'album_id' => $model->id
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
        );

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
            $content = $this->renderPartial($this->viewAlbum, array(
                'model' => $model,
                'nphotos' => $nphotos,
                'photos' => $items,
                'photos_page' => $items_page,
                'photos_per_page' => $Gallery['photos_per_page'],
                'target_id' => $this->target_id,
            ), true);
        }
        
        $this->renderPartial($this->viewContent, array('content' => $content, 'menu' => $menu, 'target_id' => $this->target_id));
    }
}

