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
    
    protected function getModule()
    {
        return Yii::app()->getModule('album');
    }
    
    protected function redirect($url, $terminate = true, $statusCode = 302)
    {
        $this->getController()->redirect($url, $terminate, $statusCode);
    }
    
    protected function render($view, $data, $return)
    {
        $result = $this->getController()->render($view, $data, $return);
        
        if ($return)
            return $result;
    }

    protected function renderPartial($view, $params, $return)
    {
        $result = $this->getController()->renderPartial($view, $params, $return);
        
        if ($return)
            return $result;
    }    
}
