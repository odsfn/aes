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
    const GALLERY_PERM_PER_ALL = 0;

    const GALLERY_PERM_PER_REGISTERED = 1;

    const GALLERY_PERM_PER_OWNER = 2; 
    
    public static $permissionLabels = array(
        self::GALLERY_PERM_PER_ALL => 'Всем',
        self::GALLERY_PERM_PER_REGISTERED => 'Только зарегистрированным пользователям',
        self::GALLERY_PERM_PER_OWNER => 'Только мне'
    );
    
    public static function albumsAsListData($target_id, $albumType = 'Album')
    {
        $albums = array(
            array('id'=>'', 'name'=>'-')
        );
        
        $albums = array_merge(
            $albums,
            $albumType::model()->findAll(
                'target_id = :targetId', 
                array(
                    ':targetId'=> $target_id
                )
            )
        );
        
        return CHtml::listData($albums, 'id', 'name');
    }

    public static function getPermissionLabel($level)
    {
        $levels = array_keys(self::$permissionLabels);
        
        if(!in_array($level, $levels))
            throw new CException('Permission level "' . $level . '" does not exist');
        
        return Yii::t('album.permissions', self::$permissionLabels[$level]);
    }

    public $albumRoute = '/album/image/album';
    
    public $imageRoute = '/album/image/photo';

    public $ajaxUpdateImageRoute = '/album/image/ajaxUpdatePhoto';
    
    public $rootRoute = '';

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
            
            'imagemod' => array('class' => 'CImageModifier')
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
            $this->getAssetsUrl();
            return true;
        } else
            return false;
    }
    
    public function getAssetsUrl($path = '')
    {
        if (!$this->assetsUrl) {
            $this->assetsUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias('album.assets'));
        }
        
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

    public function canCreateAlbum($target_id, $userId = null)
    {
        return $this->isOwner(empty($userId) ? Yii::app()->user->id : $userId, $target_id);
    }
    
    public function isOwnAlbum($album, $userId = null)
    {
        return $album->user_id == empty($userId) ? Yii::app()->user->id : $userId;
    }
    
    /**
     * Checks is the user owns target 
     * @param int $userId
     * @param int $target_id
     * @return boolean
     */
    public function isOwner($userId, $target_id)
    {
        return Target::model()->findByPk($target_id)->getRow()->user_id == $userId;
    }
}
