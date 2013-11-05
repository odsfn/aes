<?php
Yii::import('common.components.ObjectAuthAssignment');
/**
 * Provides methods to the owner which allow manage user role assignment for specific 
 * objects within application not for whole classes of them.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ObjectAuthAssignmentBehavior extends CBehavior implements iObjectAuthAssignment {
    
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
        
        if(!$this->objectId && $this->owner instanceof CActiveRecord) {
            
            $idAttr = $this->owner->getMetaData()->tableSchema->primaryKey;
            
            if(is_array($idAttr))
                throw new Exception ('Compound primary keys does not supported by ObjectAuthAssignment');

                $this->objectId = $this->owner->{$idAttr};
        } else 
            throw new Exception ('You should specify the objectId property or attach this behaviour to a CActiveRecord instance');
        
        $this->_oaa->objectType = $this->objectType;
        $this->_oaa->objectId = $this->objectId;
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
}
