<?php

class ViewGalleryItem extends GalleryBaseAction
{
    public function run()
    {
        $galleryItemType = $this->albumItemType;
        $albumType = $this->albumType;
        
        $menu = array();
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
            $menu = array(
                array('label' => 'Все видеозаписи', 'url' => array($this->getModule()->rootRoute)),
                array(
                    'label' => 'Альбом: ' . $model->album->name, 
                    'url' => array(
                        $this->getModule()->rootRoute , 
                        'action' => 'ViewAlbum', 
                        'album_id' => $album
                    )
                ),
                array('label' => 'Просмотр', 'url' => '#', 'active' => true),
            );
        else {
            $menu = array(
                array('label' => 'Все видеозаписи', 'url' => array($this->getModule()->rootRoute)),
            );

            if ($withoutAlbum)
                $menu[] = array(
                    'label' => 'Без альбома', 
                    'url' => array(
                        $this->getModule()->rootRoute , 
                        'action' => 'ViewAll', 
                        'without_album' => true
                    )
                );

            $menu[] = array('label' => 'Просмотр', 'url' => '#', 'active' => true);
        }

        $canEdit = false;
        if ($model->user_id == $user_id)
            $canEdit = true;

        $content = $this->renderPartial(
            $this->viewGalleryItem, 
            array(
                'model' => $model, 'pages' => $pages, 'canEdit' => $canEdit,
                'albumContext' => $album, 'without_album' => $withoutAlbum,
                'target_id' => $this->target_id
            ), 
            true
        );

        $this->renderPartial($this->viewContent, array('content' => $content, 'menu' => $menu));
    }
}

