<?php

class ViewAll extends GalleryBaseAction
{
    protected function ownerViewsEmptyList()
    {
        
    }

    protected function proccess($albumType, $galleryItemType)
    {
        $albums_page = Yii::app()->getRequest()->getParam('albums_page', 1);
        $items_page = Yii::app()->getRequest()->getParam('gitems_page', 1);
        $listingParams = $this->getModule()->getListingParams();
        //
        // Список альбомов цели
        //
        
        $albumsCriteria = $albumType::getAvailableAlbumsCriteria($this->target_id, $this->user_id);
        $albumsCountCriteria = clone $albumsCriteria;

        $albumsCriteria->limit = ($albums_page ? $albums_page * $listingParams['albums_per_page'] : $listingParams['albums_per_page']);
        $albumsCriteria->order = $listingParams['gitems_sort'];

        $albums = $albumType::model()->findAll($albumsCriteria);
        $nalbums = $albumType::model()->count($albumsCountCriteria);

        //
        // Список элементов альбома
        //

        $withoutAlbum = false;
        if ($_GET['without_album'])
            $withoutAlbum = true;

        $itemsCriteria = $galleryItemType::getAvailableCriteria($withoutAlbum, $this->target_id, $this->user_id);
        $itemsCountCriteria = clone $itemsCriteria;

        $itemsCriteria->limit = ($items_page ? $items_page * $listingParams['gitems_per_page'] : $listingParams['gitems_per_page']);
        $itemsCriteria->order = $listingParams['gitems_sort'];

        // Все фотографии
        $items = $galleryItemType::model()->findAll($itemsCriteria);
        $items_count = $galleryItemType::model()->count($itemsCountCriteria);

        if (!($items || $albums) && Yii::app()->user->checkAccess('album_createGItem', array('targetId' => $this->target_id)))
            $this->ownerViewsEmptyList();

        // Ajax
        if (Yii::app()->getRequest()->isAjaxRequest) {

            switch (Yii::app()->request->getQuery('view')) {
                case 'albums':
                    $output = $this->renderPartial($this->viewDefaultAlbumsAjax, array(
                        'albums' => $albums,
                        'nalbums' => $nalbums,
                        'albums_page' => $albums_page,
                        'albums_per_page' => $listingParams['albums_per_page'],
                        'target_id' => $this->target_id,
                    ), true);
                    break;
                case 'gitems':
                    $output = $this->renderPartial($this->viewDefaultItemsAjax, array(
                        'ngitems' => $items_count,
                        'gitems' => $items,
                        'gitems_page' => $items_page,
                        'gitems_per_page' => $listingParams['gitems_per_page'],
                        'target_id' => $this->target_id,
                        'without_album'=>$withoutAlbum
                    ), true);
                    break;
            }

            Yii::app()->clientScript->renderBodyEnd($output);

            echo $output;
            
            Yii::app()->end();
            
        } else {
            return $this->getController()->render($this->viewDefault, array(
                // Album
                'albums' => $albums,
                'nalbums' => $nalbums,
                'albums_page' => $albums_page,
                'albums_per_page' => $listingParams['albums_per_page'],
                // Item
                'ngitems' => $items_count,
                'gitems' => $items,
                'gitems_page' => $items_page,
                'gitems_per_page' => $listingParams['gitems_per_page'],
                'target_id' => $this->target_id,
                'without_album'=>$withoutAlbum
            ), true); 
        }
    }
}

