<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ObjectAuthAssignmentTest extends CDbTestCase {
    
    protected $fixtures = array(
        'election'         => 'Election',
        'election_auth_assignment' => ':election_auth_assignment',
        'AuthAssignment'   => ':AuthAssignment'
    );
    
    function testUserInRole() {
        $userId = 3;
        
        $objAuth1 = new ObjectAuthAssignment;
        $objAuth1->objectType = 'Election';
        $objAuth1->objectId = 1;
        
        $this->assertTrue($objAuth1->checkUserInRole($userId, 'election_creator'));
        
        $objAuth2 = new ObjectAuthAssignment;
        $objAuth2->objectType = 'Election';
        $objAuth2->objectId = 2;
        
        $this->assertFalse($objAuth2->checkUserInRole($userId, 'election_creator'));
    }
    
    function testAssignRoleToUser() {
        $getObjectAssignment = $this->commandGetObjectAssignment();
        
        $userId = 5;
        
        $objAuth2 = new ObjectAuthAssignment;
        $objAuth2->objectType = 'Election';
        $objAuth2->objectId = 2;
        
        $getAuthAssignment = $this->commandGetAuthAssignment($userId, 'election_creator');
        
        $this->assertEquals(0, $getAuthAssignment->query()->count());
        $assignment = $objAuth2->assignRoleToUser($userId, 'election_creator');
        $this->assertInstanceOf('ElectionAuthAssignment', $assignment);
        
        $result = $getAuthAssignment->queryAll();
        $this->assertEquals(1, count($result));
        
        $assignment = $result[0];
        
        $this->assertEquals(1, $getObjectAssignment->bindValues(array(':objectId' => 2, ':assignmentId' => $assignment['id']))->query()->count());
    }
    
    function testAssignRoleToUserMultipleTimesWithDifferentObjects() {
        $getObjectAssignment = $this->commandGetObjectAssignment();
        
        $userId = 5;
        
        $getAuthAssignment = $this->commandGetAuthAssignment($userId, 'election_creator');
        
        $objAuth2 = new ObjectAuthAssignment;
        $objAuth2->objectType = 'Election';
        $objAuth2->objectId = 2;
        
        $objAuth2->assignRoleToUser($userId, 'election_creator');
        
        $objAuth1 = new ObjectAuthAssignment;
        $objAuth1->objectType = 'Election';
        $objAuth1->objectId = 1;
        
        $objAuth1->assignRoleToUser($userId, 'election_creator');
        
        $result = $getAuthAssignment->queryAll();
        $this->assertEquals(1, count($result));
        
        $assignment = $result[0];
        
        $this->assertEquals(1, $getObjectAssignment->bindValues(array(':objectId' => 2, ':assignmentId' => $assignment['id']))->query()->count());
        $this->assertEquals(1, $getObjectAssignment->bindValues(array(':objectId' => 1, ':assignmentId' => $assignment['id']))->query()->count());
    }
    
    function testRevokeRoleFromUser() {
        $userId = 2;
        
        $objAuth2 = new ObjectAuthAssignment;
        $objAuth2->objectType = 'Election';
        $objAuth2->objectId = 2;
        
        $objAuth2->revokeRoleFromUser($userId, 'election_admin');
        
        $this->assertEquals(1, $this->commandGetAuthAssignment($userId, 'election_admin')->query()->count());
        $this->assertEquals(0, $this->commandGetObjectAssignment(2, 2)->query()->count());
        $this->assertEquals(1, $this->commandGetObjectAssignment(1, 2)->query()->count());
        
        $objAuth1 = new ObjectAuthAssignment;
        $objAuth1->objectType = 'Election';
        $objAuth1->objectId = 1;
        
        $objAuth1->revokeRoleFromUser($userId, 'election_admin');
        
        $this->assertEquals(1, $this->commandGetAuthAssignment($userId, 'election_admin')->query()->count());     //we will not delete entry from AuthAssignment
        $this->assertEquals(0, $this->commandGetObjectAssignment(2, 2)->query()->count());
        $this->assertEquals(0, $this->commandGetObjectAssignment(1, 2)->query()->count());
    }
    
    protected function commandGetAuthAssignment($userId = null, $itemname = 'election_creator') {
        $command = Yii::app()->db->createCommand('SELECT * FROM AuthAssignment WHERE userid = :userId AND itemname = :itemname');
        if($userId)
            $command->bindValue (':userId', $userId);
        
        if($itemname)
            $command->bindValue (':itemname', $itemname);
        
        return $command; 
    }
    
    protected function commandGetObjectAssignment($objectId = null, $assignmentId = null) {
        $command = Yii::app()->db->createCommand('SELECT * FROM election_auth_assignment WHERE object_id = :objectId AND auth_assignment_id = :assignmentId');
        
        if($objectId)
            $command->bindValue (':objectId', $objectId);
        
        if($assignmentId)
            $command->bindValue (':assignmentId', $assignmentId);
        
        return $command;
    }
}
