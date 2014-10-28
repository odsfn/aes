<?php
/**
 * This module provides functionality for photos albums representation
 * and manipulation.
 * 
 * @TODO: 
 * 
 * Components of this module based on old cold from another developers and project.
 * It should be refactored.
 * 
 * - Split ImageController to AlbumBaseController, ImageController and AlbumController
 * - ...
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class AlbumModule extends CWebModule
{

    public $albumRoute = '/album/image/album';
    
    public $imageRoute = '/album/image/photo';

    public $ajaxImageNavigation = true;

    public $imageSizeLimit = '5MB';

    protected $assetsUrl = '';

    protected function preinit()
    {   
        $this->setImport(array(
            'album.models.*',
            'album.components.taggableBehavior.*',
            'album.components.imageapi.*',
            'album.components.imagemodifier.CImageModifier',
            'album.uploadify.MUploadify'
        ));        
        
        $components = array(
            'image'=>array(
                'class'=>'album.components.imageapi.CImage',
                'allowedImageExtensions' => array("*.jpg", "*.jpeg", "*.gif", "*.png"),
                // 'thumbs' - контроллер
                // http://www.verot.net/php_class_upload_samples.htm
                'presets'=>array(
    
                    'original'=>array(
                        'cacheIn'=>'webroot.uploads.album.original',
                        'actions'=>array(),
                    ),
    
                    '160x100'=>array(
                        'cacheIn'=>'webroot.uploads.album.thumbs.160x100',
                        'actions'=>array(
                            'image_resize' => true,
                            'image_x' => 160,
                            'image_y' => 100,
                            'image_ratio_crop' => true,
                            'image_increase' => false,
                        ),
                    ),
    
                    '360x220'=>array(
                        'cacheIn'=>'webroot.uploads.album.thumbs.360x220',
                        'actions'=>array(
                            'image_resize' => true,
                            'image_x' => 360,
                            'image_y' => 220,
                            'image_ratio_crop' => true,
                            'image_increase' => false,
                        ),
                    ),
    
                    '1150x710'=>array(
                        'cacheIn'=>'webroot.uploads.album.thumbs.1150x710',
                        'actions'=>array(
                          'image_x' => 1150,
                          'image_y' => 710,
                          'image_ratio_crop' => true,
                          'image_resize' => true,
                          'image_increase' => false,
                        ),
                    ),
                ),
            ),
        );
        
        $this->setComponents($components);
        parent::preinit();
    }

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            $this->assetsUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias('album.assets'));
            return true;
        } else
            return false;
    }
    
    public function getAssetsUrl($path = '')
    {
        return $this->assetsUrl . '/' . $path;
    }
    
    /**
     * Return absolute pathes for original image and its thumbnails
     * 
     * @param string $imagePath Original image relative path. Which is get from File->path
     * @return array
     */
    public function getAbsolutePathes($imagePath)
    {
        $result = array();
        
        //extract filename from path
        $parts = explode('/', $imagePath);
        $fileName = $parts[count($parts) - 1];
        
        foreach ($this->getComponent('image')->presets as $name => $params)
        {            
            $result[] = Yii::getPathOfAlias($params['cacheIn']) . '/' . $fileName;
        }
        
        return $result;
    }
    
    const GALLERY_PERM_PER_ALL = 0;

    const GALLERY_PERM_PER_REGISTERED = 1;

    const GALLERY_PERM_PER_OWNER = 2;    
    
    public function canViewAlbum($album, $userId = null)
    {
        if (!$userId)
            $userId = Yii::app()->user->id;
        
        // Доступно только зарегестрированным
        if ($album->permission == self::GALLERY_PERM_PER_REGISTERED && Yii::app()->user->isGuest)
            return false;
        // Доступно только мне
        if ($album->permission == self::GALLERY_PERM_PER_OWNER && Yii::app()->user->id != $album->user_id)
            return false;
        
        return true;
    }
    
    // @TODO: provide configurable rules to check access items
    public function canAddPhotoToAlbum($album, $userId = null)
    {
        return $this->isOwnAlbum($album, $userId);
    }
    
    public function canDeleteAlbum($album, $userId = null)
    {
        return $this->isOwnAlbum($album, $userId);
    }
    
    public function canEditAlbum($album, $userId = null)
    {
        return $this->isOwnAlbum($album, $userId);
    }

    public function isOwnAlbum($album, $userId = null)
    {
        return $album->user_id == empty($userId) ? Yii::app()->user->id : $userId;
    }
    
    public function isOwner($userId, $target_id)
    {
        return Target::model()->findByPk($target_id)->getRow()->user_id == $userId;
    }
}
