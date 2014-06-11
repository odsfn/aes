<?php

/**
 * Provides methods that allow to handle changing of attributes
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class AttrsChangeHandlerBehavior extends CActiveRecordBehavior
{
    public $track;

    protected $changingAttrs = array();

    /**
     * Checks whather stored attribute value is differs from current
     * @param string $attrName  Name of the attribute to check
     * @return bool Returns TRUE if differs. If model is not stored yet - returns FALSE
     */
    public function isStoredDiffers($attrName)
    {
        if ($this->owner->isNewRecord)
            return false;

        return ($this->storedValue($attrName) != $this->owner->{$attrName});
    }

    /**
     * Returns value that is stored in database
     * @param string $attrName
     * @return mixed
     */
    public function storedValue($attrName)
    {
        if ($this->owner->isNewRecord)
            return null;

        $stored = $this->owner->model()->findByPk($this->owner->primaryKey);

        if (!$stored)
            return null;

        return $stored->{$attrName};
    }

    public function beforeSave($event)
    {
        $this->changingAttrs = array();

        if (!is_array($this->track))
            return;

        foreach ($this->track as $attrName) {
            if ($this->isStoredDiffers($attrName)) {
                $this->changingAttrs[$attrName] = $this->storedValue($attrName);
            }
        }
    }

    public function afterSave($event)
    {
        foreach ($this->changingAttrs as $attrName => $oldValue) {
            $this->afterStoredAttrChanged($attrName, $this->owner->$attrName, $oldValue);
        }
    }

    protected function afterStoredAttrChanged($attrName, $currentValue, $oldValue)
    {
        $attrChange = new AttributeChange($this);
        $attrChange->attribute = $attrName;
        $attrChange->currentValue = $currentValue;
        $attrChange->oldValue = $oldValue;
        $this->onAfterStoredAttrChanged($attrChange);

        $handlerName = 'afterStoredAttrChanged_' . $attrName;

        if (method_exists($this->owner, $handlerName)) {
            call_user_func_array(array($this->owner, $handlerName), array($currentValue, $oldValue, $attrName));
        }
    }

    public function onAfterStoredAttrChanged(AttributeChange $event)
    {
        $this->raiseEvent("onAfterStoredAttrChanged", $event);
    }

}

class AttributeChange extends CEvent
{
    public $attribute;

    public $currentValue;

    public $oldValue;

}
