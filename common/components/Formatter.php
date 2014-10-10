<?php
/**
 * Common formatting functions
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class Formatter {
    
    public static function toTs($value) {
        
        if(preg_match('/^0000.+/', $value))
            return 0;
        
        $date = new DateTime($value);
        return (int)$date->getTimestamp();
    }
    
    public static function fromTs($value) {
        if($value == 0)
            return '0000-00-00 00:00:00';
        
        return date('Y-m-d H:i:s', (int)$value);
    }
}
