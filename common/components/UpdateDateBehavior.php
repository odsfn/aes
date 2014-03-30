<?php

/**
 * Responses for updating datetime/date/timestamp fields of the AR on the creation
 * or update
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class UpdateDateBehavior extends CActiveRecordBehavior {
    
    /**
     * Fields to fill with current datetime in Mysql format. 
     * 
     * It can be plain array. In this case specified fields as arrays' values will
     * be updated on every save.
     * 
     * Or it can be array with keys 'update' and 'create'
     * 
     * @var array 
     */
    public $fields = array();
    
    public function beforeSave($event) {
        
        if(!empty($this->fields['update']))
            $fields = $this->fields['update']; 
        elseif(empty($this->fields['create']))
            $fields = $this->fields;
        else
            $fields = array();
        
        if($this->owner->isNewRecord && !empty($this->fields['create'])) {
            $fields = array_merge($fields, $this->fields['create']);
        }
        
        foreach ($fields as $field) {
            $this->owner->$field = date('Y-m-d H:i:s');
        }
        
        return true;
    }
    
}
