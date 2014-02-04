<?php
/**
 * Provides methods that allow to handle changing of attributes
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class AttrsChangeHandlerBehavior extends CActiveRecordBehavior {
    
    protected $_oldAttributes = array();

    public function setOldAttributes($value)
    {
        $this->_oldAttributes = $value;
    }
    
    public function getOldAttributes()
    {
        return $this->_oldAttributes;
    }

    public function afterFind($event) {
        $this->_oldAttributes = $this->owner->attributes;
    }
    
    /**
     * @param string $attrName  Attribute name
     * @return boolean Returns true if current attribut value does not match old
     */
    public function isAttrChanged($attrName) {
        return ($this->oldAttributes[$attrName] != $this->owner->{$attrName});
    }
}
