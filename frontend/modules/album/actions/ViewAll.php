<?php

class ViewAll extends GalleryBaseAction
{
    protected function ownerViewsEmptyList()
    {
        
    }

    public function run()
    {
        $albums_page = Yii::app()->getRequest()->getParam('albums_page', 1);
        $items_page = Yii::app()->getRequest()->getParam('photos_page', 1);
        $Gallery = Yii::app()->params['Gallery'];
        //
        // Список альбомов цели
        //
        $albumClass = $this->albumType;
        $albumItemClass = $this->albumItemType; 
        
        $albumsCriteria = $albumClass::getAvailableAlbumsCriteria($this->target_id, $this->user_id);
        $albumsCountCriteria = clone $albumsCriteria;

        $albumsCriteria->limit = ($albums_page ? $albums_page * $Gallery['albums_per_page'] : $Gallery['albums_per_page']);
        $albumsCriteria->order = $Gallery['photos_sort'];

        $albums = $albumClass::model()->findAll($albumsCriteria);
        $nalbums = $albumClass::model()->count($albumsCountCriteria);

        //
        // Список элементов альбома
        //

        $withoutAlbum = false;
        if ($_GET['without_album'])
            $withoutAlbum = true;

        $itemsCriteria = $albumItemClass::getAvailablePhotosCriteria($withoutAlbum, $this->target_id, $this->user_id);
        $itemsCountCriteria = clone $itemsCriteria;

        $itemsCriteria->limit = ($items_page ? $items_page * $Gallery['photos_per_page'] : $Gallery['photos_per_page']);
        $itemsCriteria->order = $Gallery['photos_sort'];

        // Все фотографии
        $items = $albumItemClass::model()->findAll($itemsCriteria);
        $items_count = $albumItemClass::model()->count($itemsCountCriteria);

        if (!($items || $albums) && $this->getModule()->isOwner($this->user_id, $this->target_id))
            $this->ownerViewsEmptyList();

        $menu = array(
            array('label' => Yii::t('album.messages', 'Все ' . $this->pluralLabel), 'url' => '#', 'active' => true),
            array(
                'label' => Yii::t('album.messages', 'Добавить ' . $this->singularLabel), 'url' => array(
                    $this->getModule()->rootRoute , 
                    'action' => 'CreateGalleryItem',
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
            array(
                'label' => Yii::t('album.messages', 'Создать альбом'), 'url' => array(
                    $this->getModule()->rootRoute,
                    'action' => 'CreateAlbum'
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
        );

        // Ajax
        if (Yii::app()->getRequest()->isAjaxRequest) {

            switch (Yii::app()->request->getQuery('view')) {
                case 'albums':
                    $output = $this->renderPartial($this->viewDefaultAlbumsAjax, array(
                        'albums' => $albums,
                        'nalbums' => $nalbums,
                        'albums_page' => $albums_page,
                        'albums_per_page' => $Gallery['albums_per_page'],
                        'target_id' => $this->target_id,
                    ), true);
                    break;
                case 'photos':
                    $output = $this->renderPartial($this->viewDefaultItemsAjax, array(
                        'nphotos' => $items_count,
                        'photos' => $items,
                        'photos_page' => $items_page,
                        'photos_per_page' => $Gallery['photos_per_page'],
                        'target_id' => $this->target_id,
                        'without_album'=>$withoutAlbum
                    ), true);
                    break;
            }

            Yii::app()->clientScript->renderBodyEnd($output);

            echo $output;
            
            Yii::app()->end();
            
        } else
            $content = $this->getController()->render($this->viewDefault, array(
                // Album
                'albums' => $albums,
                'nalbums' => $nalbums,
                'albums_page' => $albums_page,
                'albums_per_page' => $Gallery['albums_per_page'],
                // Photo
                'nphotos' => $items_count,
                'photos' => $items,
                'photos_page' => $items_page,
                'photos_per_page' => $Gallery['photos_per_page'],
                'target_id' => $this->target_id,
                'without_album'=>$withoutAlbum
            ), true);
        
        $this->getController()->render($this->viewContent, array('content' => $content, 'menu' => $menu, 'target_id' => $this->target_id));
    }
}

