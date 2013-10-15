<?php
//common functions
class AESHelper
{
    public static function arrTranslated($array)
    {
        foreach ($array as $k=>$v)
            $array[$k] = Yii::t('aes',$v);
        return $array;
    }
}