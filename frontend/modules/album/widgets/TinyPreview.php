<?php

/* 
 * Renders small album and gitem previews. May be used in navigation panel
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

    public $rootRoute = null;
    
    public $type = 'image';

    public $albumType;

    public $galleryItemType;
    
    public function init()
    {
        $this->albumRoute = $this->rootRoute;
        
        if ($this->type == 'video') {
            $this->albumType = 'VideoAlbum';
            $this->galleryItemType = 'Video';
        } else {
            $this->albumType = 'Album';
            $this->galleryItemType = 'File';
        }
        
        if (!$this->targetId)
            throw new CException('TinyPreview cant\'t be initialized. You should specify targetId');
        
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
        $albumType = $this->albumType;
        $galleryItemType = $this->galleryItemType;
        
        $albumsCriteria = $albumType::getAvailableAlbumsCriteria($this->targetId);
        $albumsCriteria->addCondition('cover_id IS NOT NULL');
        $albumsCriteria->order = '`update` DESC';
        $albumsCriteria->limit = $this->previewsCount;
        $albums = $albumType::model()->findAll($albumsCriteria);
        
        $albumsCount = count($albums);
        
        foreach ($albums as $album) {
            $imageSrc = $album->getCoverUrl();
            
            $itemUrl = $this->owner->createUrl($this->rootRoute, array(
                'action' => 'ViewAlbum',
                'album_id' => $album->id
            ));
            
            $imageUrl = $this->owner->createUrl($this->rootRoute, array(
                'action' => 'ViewGalleryItem',
                'gitem_id' => $album->cover->id,
                'album' => $album->id,
                'exact' => true
            ));
            
            $previews[] = (object)array(
                'captionHasContent' => $album->name || $album->description,
                'title' => $album->name,
                'description' => $album->description,
                'update' => $album->update,
                'itemUrl' => $itemUrl,
                'imageUrl' => $imageUrl,
                'imageSrc' => $imageSrc
            );
        }
        
        if ($albumsCount < $this->previewsCount) {
            $gitemsLimit = $this->previewsCount - $albumsCount;
            $gitemsCriteria = $galleryItemType::getAvailableCriteria($withoutAlbums = true, $this->targetId);
            $gitemsCriteria->order = '`update` DESC';
            $gitemsCriteria->limit = $gitemsLimit;
            
            $gitems = $galleryItemType::model()->findAll($gitemsCriteria);
            
            foreach ($gitems as $gitem) {
                $imageSrc = $this->module->getComponent('image')->createAbsoluteUrl('360x220', $gitem->path);
                
                $imageUrl = $this->owner->createUrl($this->rootRoute, array(
                    'action' => 'ViewGalleryItem',
                    'gitem_id' => $gitem->id,
                    'exact' => true,
                    'without_album' => true
                ));
                
                $itemUrl = $this->owner->createUrl($this->rootRoute, array(
                    'action' => 'ViewAll',
                    'without_album' => true
                ));                    

                $previews[] = (object)array(
                    'captionHasContent' => true,
                    'title' => Yii::t('album.messages', 'Без альбома'),
                    'description' => $gitem->description,
                    'update' => $gitem->update,
                    'itemUrl' => $itemUrl,
                    'imageUrl' => $imageUrl,
                    'imageSrc' => $imageSrc
                );
            }
        }
        
        return $previews;
    }
}
