<?php
//common functions
class AESHelper
{
    public static function arrTranslated($array, $namespace = 'aes')
    {
        foreach ($array as $k=>$v)
            $array[$k] = Yii::t($namespace,$v);
        return $array;
    }
    
    public static function arrTranslatedValue($array, $index, $namespace = 'aes')
    {
        $translated = AESHelper::arrTranslated($array, $namespace);
        return $translated[$index];
    }
}