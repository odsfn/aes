<?php
/**
 * Provides methods to work with uploading image of the realated ActiveRecord
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class UploadingImageBehavior extends CActiveRecordBehavior
{   
    /**
     * Name of the record's attribute with uploading image 
     * @var string
     */
    public $uploadingImageAttr = 'uploadingImage';
    
    /**
     * Name of the record's attribute with uploaded image path 
     * @var string 
     */
    public $imagePathAttr = 'image';
    
    /**
     * Attribute name with id of related AR
     * 
     * @var string 
     */
    public $idAttr = 'id';
    
    /**
     * Subpath in application.www where images will be saved. 
     * It should start with /, but should not be ended with /
     * 
     * @var string 
     */
    public $imagesDir = '/uploads';
    
    /**
     * If true then defaultImge will be used if not any other was uploaded
     * 
     * @var boolean
     */
    public $useDefault = true;

    public $defaultImage = 'unknown_user.png';

    /**
     * Spesifies sizes of image thumbnails wich will be created on image change
     * Created thumbnails pathes can be saved in owner attribute if it exists.
     * This attribute name is $imagePathAttr . '_thmbnl_' . $thumbnailSize
     * 
     * @NOTE only square thumbnails supported
     * @example Create 2 thumbnails on image chagne with sizes 64 and 128px
     *     $this->thumbnailsToCreate = array('64','128');
     * @example Will not create any thumbnails
     *     $this->thumbnailsToCreate = false;
     * @var array 
     */
    public $thumbnailsToCreate = false;
    
    public $uploadOnBeforeSave = true;
    
    /**
     * Set this attribute to an array in which first item is considered as width
     * and second as height of the resizing uploaded image. 
     * 
     * NOTE: The source image will not be stored. It will be replaced by resized one.
     * 
     * @var array
     */
    public $resize = null;
    
    protected $imageUploadingProcessed = false;

    public function __construct()
    {
        $this->attachEventHandler('onAfterImageUploaded', array($this, 'afterImageUploadedHandler'));
    }

    /**
     * Provides link to the image with specified size. Makes resizing if
     * needed
     * 
     * @param int $width
     * @param int $height
     * @return string	Path to the user's image
     * @throws CException
     */
    public function getImage($width = 64, $height = null)
    {
        $width = intval($width);
        $width || ($width = 32);

        if (!$height) {
            $height = $width;
        }

        $photosDir = $this->imagesDir;
        $basePath = Yii::app()->basePath . '/www' . $photosDir . '/';

        $image = $this->owner->{$this->imagePathAttr};
        
        if (!$image && $this->useDefault)
            $image = $this->defaultImage;

        if ($image) {
            $sizedFile = str_replace('.', '_' . $width . 'x' . $height . '.', $image);

            // Checks whather image with specified size already exists
            if (file_exists($basePath . $sizedFile))
                return Yii::app()->getBaseUrl(true) . $photosDir . "/" . $sizedFile;

            if ($this->resizeImage($basePath . $image, $basePath . $sizedFile, $width, $height))
                return Yii::app()->getBaseUrl(true) . $photosDir . "/" . $sizedFile;
        }
    } 
    
    /**
     * Sets up new image
     * @param CUploadedFile $uploadedFile
     */
    public function changeImage(CUploadedFile $uploadedFile)
    {
        $photosDir = $this->imagesDir;
        $basePath = Yii::app()->basePath . '/www' . $photosDir;

        //создаем каталог, если не существует
        if (!file_exists($basePath)) {
            mkdir($basePath);
        }

        $basePath .= '/';

        $filename = $this->owner->{$this->idAttr} . '_' . str_replace(array('.', ' '), '_', microtime()) . '.' . $uploadedFile->extensionName;

        if ($this->owner->{$this->imagePathAttr}) {
            //remove old resized images
            if (file_exists($basePath . $this->owner->{$this->imagePathAttr})) {
                unlink($basePath . $this->owner->{$this->imagePathAttr});
            }
            
            foreach (glob($basePath . $this->owner->{$this->idAttr} . '_*.*') as $oldThumbnail) {
                unlink($oldThumbnail);
            }
        }

        $uploadedFile->saveAs($filepath = $basePath . $filename);

        if(is_array($this->resize)) {
            $sizes = array_values($this->resize);
            $width = $sizes[0];
            $height = !empty($sizes[1]) ? $sizes[1] : $width;
            
            $this->resizeImage($filepath, $filepath, $width, $height);
        }
        
        $this->owner->{$this->imagePathAttr} = $filename;

        if ($this->thumbnailsToCreate && is_array($this->thumbnailsToCreate)) {
            foreach ($this->thumbnailsToCreate as $size) {
                $this->createImageThumbnail($size);
            }
        }
    }

    public function createImageThumbnail($size)
    {
        $photosDir = $this->imagesDir;
        $basePath = Yii::app()->basePath . '/www' . $photosDir . '/';

        $width = $height = $size;

        if ($this->owner->{$this->imagePathAttr}) {
            $sizedFile = str_replace('.', '_' . $width . 'x' . $height . '.', $this->owner->{$this->imagePathAttr});

            if ($this->resizeImage($basePath . $this->owner->{$this->imagePathAttr}, $basePath . $sizedFile, $width, $height)) {
                if ($this->owner->hasAttribute($this->imagePathAttr . '_thmbnl_' . $size)) {
                    $this->owner->{$this->imagePathAttr . '_thmbnl_' . $size} = $sizedFile;
                }
            }
        }
    }

    /**
     * Helper function for resizing images
     * 
     * @TODO: replace it to some image helper
     * 
     * @param string $inputFile Path to the source image
     * @param string $outputFile Path to the destination image
     * @param int $width
     * @param int $height
     * @return boolean
     */
    protected function resizeImage($inputFile, $outputFile, $width, $height)
    {
        if (file_exists($inputFile)) {
            $image = Yii::app()->image->load($inputFile);
            if ($image->ext != 'gif' || $image->config['driver'] == "ImageMagick")
                $image->resize($width, $height, CImage::WIDTH)
                        ->crop($width, $height)
                        ->quality(85)
                        ->sharpen(15)
                        ->save($outputFile);
            else
                @copy($inputFile, $outputFile);

            return true;
        }

        return false;
    }    
    
    public function beforeSave($event)
    {
        if(!$this->uploadOnBeforeSave)
            return;
        
        $this->uploadImage();
    }

    public function afterSave($event)
    {
        if($this->uploadOnBeforeSave)
            return;
     
        $this->uploadImage();
    }
    
    protected function uploadImage()
    {
        if ($this->imageUploadingProcessed)
            return;
        
        $uploadingImage = CUploadedFile::getInstance($this->owner, $this->uploadingImageAttr);

        if ($uploadingImage) {
            $this->changeImage($uploadingImage);
        }
        
        $this->afterImageUploaded();
    }
    
    protected function afterImageUploaded()
    {
        $event = new CEvent($this);
        $this->onAfterImageUploaded($event);
    }
    
    public function onAfterImageUploaded($event) {
        $this->imageUploadingProcessed = true;
        $this->raiseEvent('onAfterImageUploaded', $event);
    }
    
    public function afterImageUploadedHandler($event) {
        if ($this->uploadOnBeforeSave)
            return;
        
        if ($this->owner->isNewRecord && $this->owner->getPrimaryKey()) {
            $this->owner->setIsNewRecord(false);
            $this->owner->setScenario('update');
        }
        
        $this->owner->save(false, array($this->imagePathAttr));        
    }
}

