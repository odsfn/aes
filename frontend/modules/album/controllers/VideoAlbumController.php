<?php

/* 
 * 
 */
class VideoAlbumController extends CController
{
    public $target_id = null;
    
    public $user_id = null;

    public $viewDefault = '_default';
    
    public $viewDefaultAjax = '_default_albums_ajax';
    
    public $viewContent = '/content';
    
    protected function getAlbumType()
    {
        return 'VideoAlbum';
    }
    
    protected function getAlbumItemType()
    {
        return 'Video';
    }
    
    protected function ownerViewsEmptyList()
    {
        
    }

    public function init()
    {
        $requestedTarget = Yii::app()->request->getParam('target_id', FALSE);
        
        if(!$this->target_id && $requestedTarget)
            $this->target_id = $requestedTarget;
        else
            throw new CException('target_id should be specified');
        
        $this->user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);
        
        parent::init();
    }
    
    public function actionIndex()
    {   
        //
        // Список альбомов цели
        //
        $albumClass = $this->albumType;
        $albumItemClass = $this->albumItemType; 
        
        $albumsCriteria = $albumClass::getAvailableAlbumsCriteria($this->target_id, $this->user_id);
        $albumsCountCriteria = clone $albumsCriteria;

        $albumsCriteria->limit = ($albums_page ? $albums_page * $Gallery['albums_per_page'] : $Gallery['albums_per_page']);
        $albumsCriteria->order = $Gallery['albums_sort'];

        $albums = $albumClass::model()->findAll($albumsCriteria);
        $nalbums = $albumClass::model()->count($albumsCountCriteria);

        //
        // Список Фотографий
        //

        $withoutAlbum = false;
        if ($_GET['without_album'])
            $withoutAlbum = true;

        $photosCriteria = $albumItemClass::getAvailablePhotosCriteria($withoutAlbum, $this->target_id, $this->user_id);
        $photosCountCriteria = clone $photosCriteria;

        $photosCriteria->limit = ($photos_page ? $photos_page * $Gallery['photos_per_page'] : $Gallery['photos_per_page']);
        $photosCriteria->order = $Gallery['photos_sort'];

        // Все фотографии
        $photos = $albumItemClass::model()->findAll($photosCriteria);
        $nphotos = $albumItemClass::model()->count($photosCountCriteria);

        if (!($photos || $albums) && $this->getModule()->isOwner($this->user_id, $this->target_id))
            $this->ownerViewsEmptyList();

        $menu = array(
            array('label' => Yii::t('album.messages', 'Все видеозаписи'), 'url' => '#', 'active' => true),
            array(
                'label' => Yii::t('album.messages', 'Добавить видео'), 'url' => array(
                    $this->getModule()->albumRoute , 
                    'op' => 'upload', 
                    'target_id' => $this->target_id
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
            array(
                'label' => Yii::t('album.messages', 'Создать альбом'), 'url' => array(
                    $this->getModule()->albumRoute . '/create'
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
        );

        // Ajax
        if (Yii::app()->getRequest()->isAjaxRequest) {

            $output = $this->renderPartial($this->viewDefaultAjax, array(
                'albums' => $albums,
                'nalbums' => $nalbums,
                'albums_page' => $albums_page,
                'albums_per_page' => $Gallery['albums_per_page'],
                'target_id' => $this->target_id,
                    ), true);

            Yii::app()->clientScript->renderBodyEnd($output);

            echo $output;
            Yii::app()->end();
            
        } else
            $content = $this->renderPartial($this->viewDefault, array(
                // Album
                'albums' => $albums,
                'nalbums' => $nalbums,
                'albums_page' => $albums_page,
                'albums_per_page' => $Gallery['albums_per_page'],
                // Photo
                'nphotos' => $nphotos,
                'photos' => $photos,
                'photos_page' => $photos_page,
                'photos_per_page' => $Gallery['photos_per_page'],
                'target_id' => $this->target_id,
                'without_album'=>$withoutAlbum
            ), true);
        
        $this->renderPartial($this->viewContent, array('content' => $content, 'menu' => $menu, 'target_id' => $this->target_id));
    }
    
    public function actionCreate()
    {
        if (!$this->user_id || !$this->getModule()->canCreateAlbum($target_id, $this->user_id))
            throw new CHttpException(403);

        $albumClass = $this->albumType;
        
        $model = new $albumClass;
        if ($attributes = Yii::app()->request->getPost('Album')) {
            $model->setScenario('create');
            $model->attributes = $attributes;
            $model->target_id = $target_id;
            if ($model->save()) {
                $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'target_id' => $target_id));
            }
        }

        $menu = array(
            array('label' => Yii::t('album.messages', 'Все видеозаписи'), 'url' => array( $this->getModule()->albumRoute )),
            array('label' => Yii::t('album.messages', 'Новый альбом'), 'url' => '#', 'active' => true),
        );

        $content = $this->renderPartial('_album_create', array('model' => $model), true);
               
        $this->renderPartial('/content', array('content' => $content, 'menu' => $menu, 'target_id' => $target_id));
    }
}
