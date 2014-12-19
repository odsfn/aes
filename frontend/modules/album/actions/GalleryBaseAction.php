<?php

abstract class GalleryBaseAction extends CAction
{
    public $target_id = null;
    
    public $user_id = null;

    public $viewDefault = '_default';
    
    public $viewDefaultAjax = '_default_albums_ajax';
    
    public $viewContent = 'album.views.content';

    public $viewCreateAlbum = '_album_create';

    public $viewCreateGalleryItem = '_video_form';
    
    public $viewUpdateGalleryItem = '_video_form';
    
    public $viewGalleryItem = '_galleryItem';
    
    public $viewGalleryItemDetailsPanel = '_galleryItemDetailsPanel';
    
    public $viewItemAlbumCoverMark = '_itemAlbumCoverMark';
    
    public $viewDefaultAlbumsAjax = '_default_albums_ajax';
    
    public $viewDefaultItemsAjax = '_default_items_ajax';
    
    public $viewItemsListing = '_items_listing';

    public $viewAlbumAjax = '_album_ajax';

    public $viewAlbum = '/_album';

    public $albumType = 'Album';

    public $albumItemType = 'File';

    public $pluralLabel = 'Записи';
    
    public $singularLabel = 'Запись';
    
    public $batchAddEnabled = false;
    
    public function __construct($controller, $id, $target_id = null)
    {
        $requestedTarget = Yii::app()->request->getParam('target_id', FALSE);
        
        if(!$this->target_id && $requestedTarget)
            $this->target_id = $requestedTarget;
        elseif(!$this->target_id && $target_id)
            $this->target_id = $target_id;
        else
            throw new CException('target_id should be specified');
        
        $this->user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);
        
        parent::__construct($controller, $id);
    }

    public function run()
    {
        $galleryItemType = $this->albumItemType;
        $albumType = $this->albumType;
        $result = $this->proccess($albumType, $galleryItemType);
        
        if(is_string($result)) {
            $result = array('content' => $result);
        }
        
        $result['target_id'] = $this->target_id;
        $result['menu'] = $this->getMenu();
        $this->renderPartial($this->viewContent, $result);
    }
    
    protected function proccess($albumType, $galleryItemType)
    {
        return '';
    }

    protected function getModule()
    {
        return Yii::app()->getModule('album');
    }
    
    protected function redirect($url, $terminate = true, $statusCode = 302)
    {
        $this->getController()->redirect($url, $terminate, $statusCode);
    }
    
    protected function render($view, $data, $return = false)
    {
        $result = $this->getController()->render($view, $data, $return);
        
        if ($return)
            return $result;
    }

    protected function renderPartial($view, $params = array(), $return = false)
    {
        $result = $this->getController()->renderPartial($view, $params, $return);
        
        if ($return)
            return $result;
    }   
    
    protected function getMenu()
    {
        $commonItems = $this->getCommonMenuItems();
        
        $menu = array(
            $commonItems['viewAll'],
            $commonItems['addItem'],
            $commonItems['addAlbum']
        );
        
        $className = get_class($this);
        switch($className) {
            case 'ViewAll':
                $menu[0]['active'] = true;
                break;
            case 'AddBatchGalleryItems':
            case 'CreateGalleryItem':
                $menu[1]['active'] = true;
                break;
        }
        return $menu;
    }
    
    protected function getCommonMenuItems()
    {
        $items = array(
            'viewAll' => array(
                'label' => Yii::t('album.messages', 'Все ' . $this->pluralLabel), 
                'url' => array($this->getModule()->rootRoute)
            ),
            'viewAllWithoutAlbum' => array(
                'label' => Yii::t('album.messages', 'Без альбома'), 
                'url' => array(
                    $this->getModule()->rootRoute , 
                    'action' => 'ViewAll', 
                    'without_album' => true
                )
            ),
            'addItem' => array(
                'label' => Yii::t('album.messages', 'Добавить ' . $this->singularLabel), 'url' => array(
                    $this->getModule()->rootRoute , 
                    'action' => ($this->batchAddEnabled)? 'AddBatchGalleryItems' : 'CreateGalleryItem',
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
            'addAlbum' => array(
                'label' => Yii::t('album.messages', 'Создать альбом'), 'url' => array(
                    $this->getModule()->rootRoute,
                    'action' => 'CreateAlbum'
                ), 
                'visible' => $this->getModule()->isOwner($this->user_id, $this->target_id)
            ),
            'viewing' => array(
                'label' => Yii::t('album.messages', 'Просмотр'), 
                'url' => '#', 
                'active' => true
            ),
            'editing' => array(
                'label' => Yii::t('album.messages', 'Редактировать'), 
                'url' => '#', 
                'active' => true
            )
        );
        
        return $items;
    }
}
