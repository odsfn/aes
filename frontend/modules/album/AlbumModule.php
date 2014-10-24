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
    
                    '100x100'=>array(
                        'cacheIn'=>'webroot.uploads.album.thumbs.100x100',
                        'actions'=>array(
                          'image_x' => 100,
                          'image_y' => 100,
                          'image_ratio_crop' => true,
                          'image_resize' => true,
//                          'image_watermark' => '',
                          'image_increase' => false,
                        ),
                    ),
    //
    //                '200x200'=>array(
    //                    'cacheIn'=>'webroot.uploads.thumbs.200x200',
    //                    'actions'=>array(
    //                      'image_x' => 200,
    //                      'image_y' => 200,
    //                      'image_ratio_crop' => true,
    //                      'image_resize' => true,
    //                      'image_watermark' => 'logo70x45.png',
    //                      'image_watermark_path' => 'webroot.commons',
    //                      'image_watermark_x' => -10,
    //                      'image_watermark_y' => -10,
    //                      'image_increase' => false,
    //                    ),
    //                ),
    
                    '600x480'=>array(
                        'cacheIn'=>'webroot.uploads.album.thumbs.600x480',
                        'actions'=>array(
                          'image_x' => 600,
                          'image_y' => 480,
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
