<?php

class Gallery extends CWidget
{
    protected $_action;

    public $target_id;
    
    public $type = 'video';

    public static function actions()
    {
        return array(
            'ViewAll'=>'album.actions.ViewAll',
            'ViewAlbum'=>'album.actions.ViewAlbum',
            'CreateAlbum'=>'album.actions.CreateAlbum',
            'UpdateAlbum'=>'album.actions.UpdateAlbum',
            'DeleteAlbum'=>'album.actions.DeleteAlbum',
            'CreateGalleryItem'=>'album.actions.CreateGalleryItem',
            'UpdateGalleryItem'=>'album.actions.UpdateGalleryItem',
            'DeleteGalleryItem'=>'album.actions.DeleteGalleryItem',
            'ViewGalleryItem'=>'album.actions.ViewGalleryItem'
        );
    }
    
    public function init()
    {
        Yii::import('album.AlbumModule');
        Yii::import('album.actions.*');
        Yii::import('album.models.*');
        
        parent::init();
    }

    public function getViewPath($checkTheme = false)
    {
        $path = Yii::getPathOfAlias('album.views.videoAlbum');
        return $path;
    }

    public function renderPartial($view, $params, $return = false)
    {
        $result = $this->render($view, $params, $return);
        
        if ($return)
            return $result;
    }

    public function getModule()
    {
        return Yii::app()->getModule('album');
    }
    
    public function createUrl($route, $params = array())
    {
        return $this->getController()->createUrl($route, $params);
    }

    public function redirect($url, $terminate = true, $statusCode = 302)
    {
        $this->getController()->redirect($url, $terminate, $statusCode);
    }
    
    public function getAction()
    {
        return $this->_action;
    }

    public function run()
    {
        Yii::import('album.AlbumModule');
        Yii::import('album.actions.*');
        Yii::import('album.models.*');
        
        $action = Yii::app()->request->getParam('action', 'ViewAll');
        
        $action = new $action($this, $this->id, $this->target_id);
        
        if ($this->type == 'video') {
            $action->albumType = 'VideoAlbum';
            $action->albumItemType = 'Video';
            $action->pluralLabel = 'Видеозаписи';
            $action->singularLabel = 'Видеозапись';
        } else {
            $action->albumType = 'Album';
            $action->albumItemType = 'File';
            $action->pluralLabel = 'Фотографии';
            $action->singularLabel = 'Фото';
        }
        
        $this->_action = $action;
        
        $action->run();
    }
}

