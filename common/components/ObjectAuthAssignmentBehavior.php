<?php
Yii::import('common.components.ObjectAuthAssignment');
/**
 * Provides methods to the owner which allow manage user role assignment for specific 
 * objects within application not for whole classes of them.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ObjectAuthAssignmentBehavior extends CActiveRecordBehavior implements iObjectAuthAssignment {
    
    /**
     * @var ObjectAuthAssignment
     */
    protected $_oaa;

    public $objectType;

    public $objectId;

    public function attach($owner) {
        
        parent::attach($owner);
        
        $this->_oaa = new ObjectAuthAssignment;
        
        if(!$this->objectType)
            $this->objectType = get_class($this->owner);
        
        if(!$this->objectId)
            $this->setObjectId($this->getObjectIdFromOwner());
        
        $this->_oaa->objectType = $this->objectType;
    }

    public function detach($owner) {
        parent::detach($owner);
        
        $this->objectId = null;
        $this->objectType = null;
        $this->_oaa = null;
    }
    
    public function checkUserInRole($userId, $roleName) {
        return $this->_oaa->checkUserInRole($userId, $roleName);
    }
    
    public function assignRoleToUser($userId, $roleName) {
        return $this->_oaa->assignRoleToUser($userId, $roleName);
    }
    
    public function revokeRoleFromUser($userId, $roleName) {
        return $this->_oaa->revokeRoleFromUser($userId, $roleName);
    }
    
    public function afterSave($event) {
        if(!$this->objectId)
            $this->setObjectId($this->getObjectIdFromOwner());
    }
    
    protected function getObjectIdFromOwner() {
        
        $idAttr = $this->owner->getMetaData()->tableSchema->primaryKey;

        if(is_array($idAttr))
            throw new Exception ('Compound primary keys does not supported by ObjectAuthAssignment');

        return $this->owner->{$idAttr};

    }
    
    public function setObjectId($objectId) {
        $this->objectId = $objectId;
        $this->_oaa->objectId = $this->objectId;
    }
}
