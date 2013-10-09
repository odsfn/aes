<?php
/**
 * The set of the array helpers
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ArrayHelper {
    /**
     * Checks whether the passed array is associative
     * @param array $array Array that will be checked
     * @return boolean
     */
    public static function isAssoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
    
    /**
     * Formats specified fields of the multidimetional array by formatters
     * 
     * @param array $array Multidimentional associative array which will be formated
     * @param array $name Description of the formatters. Keys represents path of associative keys, value is the callable which 
     * gets value as argument and returns formatted.
     */
    public static function format($array, $formatters) {
        if($array[0]['id'] == 13)
            Yii::log(var_export($array, true), CLogger::LEVEL_INFO);
        
        if(count($formatters) === 0)
            return $array;
        
        if(!is_array($array))
            throw new Exception('Only arrays can be formatted by ArrayHelper::format function');
        
        foreach($formatters as $formatField => $formatOperator) {

            if(strstr($formatField, '.'))
                $pathToValue = explode('.', $formatField);
            else
                $pathToValue = array($formatField);

            $node = &$array;

            while(count($pathToValue)) {
                $curKey = array_shift($pathToValue);

                if(isset($node[$curKey]))
                    $node = &$node[$curKey];
                elseif(!self::isAssoc($node)) {
                    foreach ($node as $index => $row)
                        $node[$index] = self::format($row, array(implode('.', array_merge(array($curKey), $pathToValue)) => $formatOperator));
                    
                    continue(2);
                }else
                    continue(2);
            }

            if(is_callable($formatOperator)) {
                $node = call_user_func($formatOperator, $node);
            }else
                throw new Exception ('You should provide correct callable.');

        }            
        
        return $array;        
    }
}
