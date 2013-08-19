<?php
/**
 * Обрамляет изображение в рамку, фиксируя его по центру.
 */
class ImageWrapper extends CWidget
{
    public $width = 100;
    /**
     * Для квадрата достаточно указать только $width
     * @var string 
     */
    public $height;
    /**
     * Цвет рамки 
     * @var string 
     */
    public $backgroundColor = '#000';
    /**
     * URL изображения, помещаемого в рамку
     * @var string
     */
    public $imageSrc;
    /**
     * Альтернативный текст изображения
     * @var string 
     */
    public $imageAlt = '';
    /**
     * Если истина - предотвращает кэширование добавлением значения microtime()
     * к src изображения
     * @var boolean 
     */
    public $noCache = false;
    
    public $htmlOptions = array();
    
    public function run() {
        if(!$this->height) {
            $this->height = $this->width;
        }
        
        Yii::app()->clientScript->registerCssFile(
            Yii::app()->assetManager->publish(
                Yii::getPathOfAlias('common.widgets.imageWrapper.assets').'/image-wrapper.css'
            )
        );
        
        $htmlOptions = array(
            'class' => 'img-wrapper-tocenter',
            'style' => 'width: ' . $this->width . '; height: ' . $this->height . '; background-color: '. $this->backgroundColor .';',
        );
        
        if(isset($this->htmlOptions['class'])) {
            $class = $this->htmlOptions['class'];
            unset($this->htmlOptions['class']);
            $htmlOptions['class'] .= ' ' . $class;
        }
        
        if(isset($this->htmlOptions['style'])) {
            $style = $this->htmlOptions['style'];
            unset($this->htmlOptions['style']);
            $htmlOptions['style'] .= ' ' . $style;
        }
        
        if(is_array($this->htmlOptions) && count($this->htmlOptions) > 0)
            $htmlOptions = array_merge($htmlOptions, $this->htmlOptions);
        
        echo CHtml::tag('div', $htmlOptions, '<span></span>' . CHtml::image($this->imageSrc . ( $this->noCache ? '?'.microtime() : ''),  $this->imageAlt));
    }
}