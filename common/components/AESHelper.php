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
    
    public static function explode($set, $delim = ',') {
        if(is_string($set)) {
            if(strstr($set, $delim) !== FALSE) {
                
                if ($delim === ' ') {
                    $result = explode($delim, preg_replace('/\s{1,}/', ' ', trim($set)));
                }else{
                    $result = explode($delim, preg_replace('/\s+/', '', $set));
                }
            } else
                $result = array(trim($set));
        } elseif (is_array($set)) {
            $result = $set;
        } else
             throw new Exception('Invalid set format');
        
        return $result;
    }
}