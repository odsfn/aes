<?php
/**
 * By default RBAC roles are assigned to the classes for all application. This class
 * extends model with controlable access by methods that allow manage user role assignment for specific 
 * objects within application but not for whole classes of them.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ObjectAuthAssignment implements iObjectAuthAssignment {
    
    public $objectType;
    
    public $objectId;
    
    protected $db;

    public function __construct() {
        $this->db = Yii::app()->db;
    }

    public function checkUserInRole($userId, $roleName) {
        
        $command = $this->db->createCommand('SELECT oa.id FROM ' . $this->getTable() . ' oa, AuthAssignment aa ' 
                . 'WHERE aa.userid = :userId AND aa.itemname = :roleName AND oa.auth_assignment_id = aa.id AND oa.object_id = :objectId'
        );
        $command->bindValue(':userId', $userId);
        $command->bindValue(':roleName', $roleName);
        $command->bindValue(':objectId', $this->objectId);
        $result = $command->query();
        
        return (bool)$result->count();
    }
    
    public function assignRoleToUser($userId, $roleName) {
        
        $auth = Yii::app()->authManager;
        
        if(!$auth->getAuthItem($roleName))
            throw new Exception('Specified $roleName (' . $roleName . ') was not found in the RBAC hierarchy.');
        
        $assignedAuthItem = self::fetchAuthAssignment($userId, $roleName);
        
        if(!$assignedAuthItem) {
            $auth->assign($roleName, $userId);
            $assignedAuthItem = self::fetchAuthAssignment($userId, $roleName);
        }
        
        $authAssignmentId = $assignedAuthItem['id'];
                
        $authAssignment = $this->createAuthAssignment();
        $authAssignment->auth_assignment_id = $authAssignmentId;
        $authAssignment->object_id = $this->objectId;
        
        if(!$authAssignment->save())
            throw new Exception('Can\'t assign role ' . $roleName . ' to user ' . $userId . ' within object ' . $this->objectId . 'Error: ' . print_r($authAssignment->errors, true));
        
        return $authAssignment;
    }
    
    public function revokeRoleFromUser($userId, $roleName) {
        $auth = Yii::app()->authManager;
        
        if(!$auth->getAuthItem($roleName))
            throw new Exception('Specified $roleName (' . $roleName . ') was not found in the RBAC hierarchy.');
        
        $assignedAuthItem = self::fetchAuthAssignment($userId, $roleName);
        
        if(!$assignedAuthItem) 
            return false;
        
        $result = $this->db->createCommand()
                ->delete($this->getTable(), 'auth_assignment_id = :authAssignmentId AND object_id = :objectId', 
                        array(
                            ':authAssignmentId'=>$assignedAuthItem['id'], 
                            ':objectId'=>$this->objectId
                        )
        );
        
        return (bool)$result;
    }
    
    public function getTable() {
        return $this->getObjectTypeId() . '_auth_assignment';
    }
    
    public function getObjectTypeId() {
        return lcfirst($this->objectType);
    }
    
    /**
     * Return prepared command for fetching users with assigned roles on current object
     * @param string $authItem
     * @param boolean $traverse
     * @return CDbCommand
     * @throws Exception
     */
    public function getFetchAssignedWithAccessCommand($authItem, $traverse = true) {
        
        $auth = Yii::app()->authManager;
        $authItems = array();
        
        
        if(!($item = $auth->getAuthItem($authItem)))
            throw new Exception ('Specified authItem "'. $authItem .'" was not found');
        
        $authItems[] = $authItem;
        
        if($traverse) {
            $ancestors = $auth->getAncestors($authItem);
            $authItems = array_merge($authItems, $ancestors);
        }
        
        foreach ($authItems as $index => $item) {
            $authItems[$index] = '"'.$item.'"';
        }
        
        $authItems = implode(',', $authItems);

        $command = Yii::app()->db->createCommand()
                ->select('aa.id, aa.itemname, aa.userid')
                ->from($this->getTable() . ' oa')
                ->join('AuthAssignment aa', 
                        array('and', 
                            'aa.itemname IN(' . $authItems . ')',
                            'oa.auth_assignment_id = aa.id'
                        )
                )
                ->where(array('and', 'oa.object_id = :objectId'), array(':objectId' => $this->objectId));
        
        return $command;
    }

    public function createAuthAssignment() {
        $assignment = $this->objectType . 'AuthAssignment';
        return new $assignment();
    }

    public function fetchAssignedWithAccess($authItem, $traverse = true) {
        $command = $this->getFetchAssignedWithAccessCommand($authItem, $traverse);
        
        return $command->queryAll();
    }
    
    public static function fetchAuthAssignment($userId, $roleName) {
        return Yii::app()->db->createCommand()
                ->select()
                ->from('AuthAssignment')
                ->where('itemname = :roleName AND userid = :userId', array(':roleName'=>$roleName, ':userId'=>$userId))
                ->queryRow(true);
    }
}
