<?php
/**
 * TransformAttributesBehavior class file.
 *
 * @author Annenkov Yaroslav <ya@annenkov.ru>
 * @author Vasiliy Pedak <truvazia@gmail.com>
 * @link https://github.com/vasiliy-pdk/transform-attributes-behavior
 */

/**
 * Behavior for Yii 1.x CActiveRecord
 * Transform values of attributes before saving to DB and after reading from DB.
 * 
 * @method CActiveRecord getOwner()
 * @version 0.1.1
 */
class TransformAttributesBehavior extends CActiveRecordBehavior
{
    private $_backupAttributes = array();
    private $_saving = false;
    public $callbackToDb;
    public $callbackFromDb;
    public $transformations = array();

    public function events()
    {
        return array(
            'onBeforeSave' => 'beforeSave',
            'onAfterSave' => 'afterSave',
            'onAfterFind' => 'afterFind',
        );
    }

    function __construct()
    {
        // default callback function for save to db
        $this->callbackToDb = function ($model, $attributeName) {
            if (empty($model->$attributeName) || is_string($model->$attributeName))
                return $model->$attributeName;
            else
                return CJSON::encode($model->$attributeName);
        };
        // default callback function for read from db
        $this->callbackFromDb = function ($model, $attributeName) {
            return empty($model->$attributeName) ? $model->$attributeName : CJSON::decode($model->$attributeName);
        };
    }

    /**
     * @param CEvent $event
     */
    public function beforeSave($event)
    {
        $this->_saving = true;
        $this->_convertAttributesToDB();
        parent::beforeSave($event);
    }

    /**
     * @param CEvent $event
     */
    public function afterSave($event)
    {
        // restore values of attributes saved in _convertAttributesToDB()
        if(count($this->_backupAttributes)) {
            foreach($this->_backupAttributes as $name => $value) {
                $this->getOwner()->$name = $value;
            }
            $this->_backupAttributes = array();
        }
        $this->_saving = false;
        parent::afterSave($event);
    }

    /**
     * @param CEvent $event
     */
    public function afterFind($event)
    {
        $this->_convertAttributesFromDB();
        parent::afterFind($event);
    }

    public function getUnserializedAttr($attr)
    {
        if(!in_array($attr, $this->transformations))
            return null;
        
        if($this->_saving)
            return $this->_backupAttributes[$attr];
        
        return $this->getOwner()->$attr;
    }

    /**
     * Convert values of attributes before saving to DB
     *
     * @see attributeConverted()
     */
    private function _convertAttributesToDB()
    {
        $owner = $this->getOwner();
        if($attributes = $this->_getTransformations()) {
            $this->_backupAttributes = array_merge($this->_backupAttributes, $owner->getAttributes(array_keys($attributes)));
            foreach($attributes as $name => $value) {
                if(isset($value['to']) && is_callable($value['to'])) {
                    $callback = $value['to'];
                } else {
                    $callback = $this->callbackToDb;
                }
                $owner->$name = $callback($owner, $name);
            }
        }
    }

    /**
     * Convert values of attributes after reading from DB
     *
     * @see attributeConverted()
     */
    private function _convertAttributesFromDB()
    {
        $owner = $this->getOwner();
        if($attributes = $this->_getTransformations()) {
            foreach($attributes as $name => $value) {
                if(isset($value['from']) && is_callable($value['from'])) {
                    $callback = $value['from'];
                } else {
                    $callback = $this->callbackFromDb;
                }
                $owner->$name = $callback($owner, $name);
            }
        }
    }

    /**
     * Get array of transformations attributes.
     * If in model exists method attributeTransformations(), data is fetched from him
     * or from $this->transformations.
     *
     * @return array
     */
    private function _getTransformations()
    {
        if(method_exists($this->getOwner(), 'attributeTransformations')) {
            $transformations = $this->getOwner()->attributeTransformations();
        } else {
            $transformations = $this->transformations;
        }
        foreach($transformations as $key => $value) {
            if(is_numeric($key)) {
                $transformations[$value] = array();
                unset($transformations[$key]);
            }
        }
        return $transformations;
    }

}