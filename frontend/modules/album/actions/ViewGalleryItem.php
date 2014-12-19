<?php

class ViewGalleryItem extends GalleryBaseAction
{
   
    protected $album;
    
    protected function proccess()
    {
        $galleryItemType = $this->albumItemType;
        $albumType = $this->albumType;
        $user_id = $this->user_id;
        $target_id = $this->target_id;
        $Gallery = Yii::app()->params['Gallery'];

        $item_id = Yii::app()->request->getParam('photo_id', 0);
        $album = Yii::app()->request->getParam('album', 0);
        $page = Yii::app()->request->getParam('page', 1);
        
        $model = $galleryItemType::model()->with('album')->findByPk($item_id);
        
        $withoutAlbum = Yii::app()->request->getParam('without_album', false);
                
        $criteria = $galleryItemType::getAvailablePhotosCriteria($withoutAlbum, $target_id, $user_id, $album);
        $criteria->order = $Gallery['photos_sort'];

        // Specified photo, so we have to search it in the navigation set
        if ($item_id && !empty($_GET['exact'])) {
            $positionCriteria = clone $criteria;
            $positionCriteria->select = '*, COUNT(id) as page';

            $compareOp = '<';
            if (preg_match('/DESC/i' ,$positionCriteria->order))
                $compareOp = '>';

            $positionCriteria->addCondition('id '.$compareOp.'= ' . (int)$item_id);

            $tableSchema = $galleryItemType::model()->getTableSchema();
            $command = $galleryItemType::model()->getCommandBuilder()->createFindCommand($tableSchema, $positionCriteria);

            $result = $command->queryRow();
            $page = $result['page'];

            $_GET['page'] = $page;
        }

        $pages = new CPagination($galleryItemType::model()->count($criteria));
        $pages->route = $this->getModule()->rootRoute;
        $pagerParams = array(
            'action' => 'ViewGalleryItem',
            'album' => $album
        );

        if ($withoutAlbum)
            $pagerParams['without_album'] = $withoutAlbum;

        $pages->params = $pagerParams;
        $pages->pageSize = 1;
        $pages->applyLimit($criteria);

        $model = $galleryItemType::model()->find($criteria);

        if (!empty($album) && isset($model->album))
            $this->album = $model->album;

        $canEdit = false;
        if ($model->user_id == $user_id)
            $canEdit = true;

        return $this->renderPartial(
            $this->viewGalleryItem, 
            array(
                'model' => $model, 'pages' => $pages, 'canEdit' => $canEdit,
                'albumContext' => $album, 'without_album' => $withoutAlbum,
                'target_id' => $this->target_id
            ), 
            true
        );
    }
    
    protected function getMenu()
    {
        $items = $this->getCommonMenuItems();
        $menu = array(
            $items['viewAll'],
            $items['viewing']
        );
        
        if($this->album) {
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
        } elseif(Yii::app()->request->getParam('without_album', false)) {
            array_splice($menu, 1, 0, array($items['viewAllWithoutAlbum']));
        }
        
        return $menu;
    }
}

