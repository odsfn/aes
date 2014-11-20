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

    public $albumRoute = null;

    public function init()
    {
        if (!$this->targetId)
            throw new CException('TinyPreview cant\'t be initialized. You should specify targetId');

        if (!$this->albumRoute)
            $this->albumRoute = $this->module->albumRoute;
        
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
        $albumsCriteria->limit = $this->previewsCount;
        $albums = Album::model()->findAll($albumsCriteria);
        
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
        
        return $previews;
    }
}
