<?php
/**
 * AesWidgetFactory extends standard widget factory to add custom widget instantiation
 * helper functions
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class AesWidgetFactory extends CWidgetFactory {
    
    public function createWidget($owner, $className, $properties = array()) {
        
        $matches = array();
        
        if( !strstr('.', $className) &&  preg_match('/^(.+)MarionetteWidget$/', $className, $matches)) {
            
            $baseClassName = 'frontend.widgets.MarionetteWidget';
            
            $baseClassName=Yii::import($baseClassName,true);
            
            $widget = new $baseClassName($owner);
            
        } else {

            $className=Yii::import($className,true);

            $widget=new $className($owner);
            
        }
        
        if(isset($this->widgets[$className]))
                $properties=$properties===array() ? $this->widgets[$className] : CMap::mergeArray($this->widgets[$className],$properties);
        if($this->enableSkin)
        {
                if($this->skinnableWidgets===null || in_array($className,$this->skinnableWidgets))
                {
                        $skinName=isset($properties['skin']) ? $properties['skin'] : 'default';
                        if($skinName!==false && ($skin=$this->getSkin($className,$skinName))!==array())
                                $properties=$properties===array() ? $skin : CMap::mergeArray($skin,$properties);
                }
        }
        
        foreach($properties as $name=>$value)
                $widget->$name=$value;
        
        return $widget;
    }
    
}
