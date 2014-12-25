<?php
/**
 * This module provides functionality for photo and video albums representation
 * and manipulation.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class AlbumModule extends CWebModule
{
    const GALLERY_PERM_PER_ALL = 0;

    const GALLERY_PERM_PER_REGISTERED = 1;

    const GALLERY_PERM_PER_OWNER = 2; 
    
    public $rootRoute = '';

    public $ajaxImageNavigation = true;
    
    public $imageSizeLimit = '5MB';

    public $viewAfterGalleryItemDetails = '_afterGalleryItemDetails';
    
    public $albums_per_page = 6;
    public $albums_per_line = 3;
    public $gitems_per_line = 6;
    public $gitems_per_page = 24;
    public $gitems_sort = 't.id DESC';
    public $albums_sort = 't.update DESC';
    public $previewsCount = 2; // count of previews in navigation sidebar
    
    protected $assetsUrl = '';
    
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

    protected function preinit()
    {   
        $this->setImport(array(
            'album.models.*',
            'album.components.iGalleryItem',
            'album.components.iDownloadable',
            'album.components.taggableBehavior.*',
            'album.components.imageapi.*',
            'album.components.imagemodifier.CImageModifier',
            'album.uploadify.MUploadify'
        ));        
        
        $components = array(
            'image'=>array(
                'class'=>'album.components.imageapi.CImage',
                'allowedImageExtensions' => array("*.jpg", "*.jpeg", "*.gif", "*.png"),
                // thumbnails settings
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
        $this->registerDefaultRoles();
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
    
    public function getListingParams()
    {
        $params = array();
        $attrs = array(
            'albums_per_page',
            'albums_per_line',
            'gitems_per_line',
            'gitems_per_page',
            'gitems_sort',
            'albums_sort',
            'previewsCount'
        );
        
        foreach ($attrs as $attrName) {
            $params[$attrName] = $this->$attrName;
        }
        
        return $params;
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
        return Yii::app()->user->checkAccess('album_viewGItem', array('item' => $album));
    }
    
    // @TODO: provide configurable rules to check access items
    public function canAddItemToAlbum($album, $userId = null)
    {
        return Yii::app()->user->checkAccess('album_createGItem', array('item' => $album));
    }
    
    public function canDeleteAlbum($album, $userId = null)
    {
        return Yii::app()->user->checkAccess('album_deleteGItem', array('item' => $album));
    }
    
    public function canEditAlbum($album, $userId = null)
    {
        return Yii::app()->user->checkAccess('album_editGItem', array('item' => $album));
    }

    public function canCreateAlbum($target_id, $userId = null)
    {
        return Yii::app()->user->checkAccess('album_createGItem', array('targetId' => $target_id));
    }
    
    /**
     * Creates thumbnails for currently uploaded images
     * @param string $file_path Path to an image which has been currently uploaded
     */
    public function createThumbnails($file_path, $overwrite = false)
    {
        $this->getComponent('image')->createPath('160x100', $file_path, false, $overwrite);
        $this->getComponent('image')->createPath('1150x710', $file_path, false, $overwrite);
    }
    
    public function createAlbumThumbnail($file_path)
    {
        $this->getComponent('image')->createPath('360x220', $file_path);
    }
    
    protected function registerDefaultRoles()
    {
        Yii::app()->authManager->defaultRoles = array_merge(
            Yii::app()->authManager->defaultRoles,
            array('album_notAuthenticated', 'album_authenticated')
        );
    }
}
