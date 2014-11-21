<?php

/* 
 * Renders small album and photo previews. May be used in navigation panel
 */
class TinyPreview extends CWidget
{
    private $module; 
    
    public $isModuleSelected = false;

    public $targetId = null;
    
    public $previewsCount = 2;

    public $titleText = 'Photos';
    
    public $albumRoute = null;
    
    public $imageRoute = null;

    public function init()
    {
        if (!$this->targetId)
            throw new CException('TinyPreview cant\'t be initialized. You should specify targetId');

        if (!$this->albumRoute)
            $this->albumRoute = $this->module->albumRoute;
        
        if (!$this->imageRoute)
            $this->imageRoute = $this->module->imageRoute;
        
        $this->module = Yii::app()->getModule('album');
        
        Yii::app()->clientScript
            ->registerCssFile($this->module->getAssetsUrl('css/thumbnails.css'))
            ->registerScriptFile($this->module->getAssetsUrl('js/captionTransparent.js'));
    }
 
    public function run()
    {
        $previews = array();
        
        if (!$this->isModuleSelected) {
            $previews = $this->fetchPreviews();
        }
        
        $this->render('tinyPreview', array('previews'=>$previews));
    }
    
    protected function fetchPreviews()
    {
        $previews = array();
        
        $albumsCriteria = Album::getAvailableAlbumsCriteria($this->targetId);
        $albumsCriteria->addCondition('path IS NOT NULL');
        $albumsCriteria->order = '`update` DESC';
        $albumsCriteria->limit = $this->previewsCount;
        $albums = Album::model()->findAll($albumsCriteria);
        
        $albumsCount = count($albums);
        
        foreach ($albums as $album) {
            $imageSrc = $this->module->getComponent('image')->createAbsoluteUrl('360x220', $album->path);
            $itemUrl = $this->owner->createUrl($this->albumRoute, array(
                'op' => 'view',
                'album_id' => $album->id,
                'target_id' => $this->targetId
            ));
            
            $previews[] = (object)array(
                'captionHasContent' => $album->name || $album->description,
                'title' => $album->name,
                'description' => $album->description,
                'update' => $album->update,
                'itemUrl' => $itemUrl,
                'imageUrl' => $itemUrl,
                'imageSrc' => $imageSrc
            );
        }
        
        if ($albumsCount < $this->previewsCount) {
            $photosLimit = $this->previewsCount - $albumsCount;
            $photosCriteria = File::getAvailablePhotosCriteria($withoutAlbums = true, $this->targetId);
            $photosCriteria->order = '`update` DESC';
            $photosCriteria->limit = $photosLimit;
            
            $photos = File::model()->findAll($photosCriteria);
            
            foreach ($photos as $photo) {
                $imageSrc = $this->module->getComponent('image')->createAbsoluteUrl('360x220', $photo->path);
                $imageUrl = $this->owner->createUrl($this->imageRoute, array(
                    'op' => 'view',
                    'photo_id' => $photo->id,
                    'target_id' => $this->targetId,
                    'exact' => true
                ));
                $itemUrl = $this->owner->createUrl($this->imageRoute, array(
                    'op' => 'view',
                    'target_id' => $this->targetId
                ));

                $previews[] = (object)array(
                    'captionHasContent' => true,
                    'title' => Yii::t('album.messages', 'Без альбома'),
                    'description' => $photo->description,
                    'update' => $photo->update,
                    'itemUrl' => $itemUrl,
                    'imageUrl' => $imageUrl,
                    'imageSrc' => $imageSrc
                );
            }
        }
        
        return $previews;
    }
}