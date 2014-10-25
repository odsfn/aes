<?php

/**
 * This module provides functionality for photos and videos albums representation
 * and manipulation
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class AlbumModule extends CWebModule
{

    public $albumRoute = '/album/image/album';
    
    public $imageRoute = '/album/image/photo';

    public $ajaxImageNavigation = true;

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
}
