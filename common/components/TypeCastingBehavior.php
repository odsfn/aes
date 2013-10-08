<?php
/**
 * Performs type casting for numeric values.
 * Problem described here: http://www.yiiframework.com/forum/index.php/topic/34249-attribute-retrieved-as-string-instead-of-int/
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class TypeCastingBehavior extends CActiveRecordBehavior {

    public function useTypeCasting()
    {
        return true;
    }
    
    public function afterFind($event) {
        
        $model = $this->owner;
        
        $attributes = $model->getAttributes();
        
        if ($this->useTypeCasting() && is_array($attributes)) {
            foreach ($attributes as $name => &$value)
                $type = $model->getMetaData()->columns[$name]->type;
                if($type)
                    settype($value, $type);
        
            $model->setAttributes($attributes, false);
        }
    }
    
}