<?php
class ElectorRegistrationRequestTest extends CDbTestCase
{
    public $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'election' => array('Election', 'unit/electorRegistrationRequest/election'),
        'elector' => array('Elector', 'unit/electorRegistrationRequest/elector'),
        'elector_registration_request' => array(
            'ElectorRegistrationRequest', 
            'unit/electorRegistrationRequest/elector_registration_request'
        ),
        'voter_group' => array('VoterGroup', 'unit/electorRegistrationRequest/voter_group'),
        'voter_group_member' => array('VoterGroupMember', 'unit/electorRegistrationRequest/voter_group_member'),
        'election_voter_group' => array('ElectionVoterGroup', 'unit/electorRegistrationRequest/election_voter_group')
    );
    
    public function testRegistrationRequestedByUserInElectionWithConfirmationNeeded()
    {
        $userId = 2;
                
        $err = new ElectorRegistrationRequest();
        $err->election_id = 1;
        $err->initiator_id = $userId;
        $err->user_id = $userId;
        $err->save();
        
        $errId = $err->id;
        $this->assertGreaterThan(0, $errId);
        $this->assertEquals($err->status, ElectorRegistrationRequest::STATUS_AWAITING_ADMIN_DECISION);
        
        $row = Yii::app()->db->createCommand(
            'SELECT * FROM elector_registration_request WHERE id = ' . $errId
        )->queryRow(true);
        
        $this->assertEquals($err->getAttributes(), $row);
        
        $electors = Elector::model()->findAllByAttributes(array(
            'election_id'=>$err->election_id,
            'user_id'=>$err->user_id
        ));
        $this->assertCount(0, $electors);        
    }
    
    public function testAllowRegistration()
    {
        $userId = 2;
                
        $err = new ElectorRegistrationRequest();
        $err->election_id = 1;
        $err->initiator_id = $userId;
        $err->user_id = $userId;
        $err->save();
        
        $err->status = ElectorRegistrationRequest::STATUS_REGISTERED;
        $err->save();
        $this->assertEquals(ElectorRegistrationRequest::STATUS_REGISTERED, $err->status);
        
        $row = Yii::app()->db->createCommand(
            'SELECT * FROM elector_registration_request WHERE id = ' . $err->id
        )->queryRow(true);
        
        $this->assertEquals($err->getAttributes(), $row);        
        
        $electors = Elector::model()->findAllByAttributes(array(
            'election_id'=>$err->election_id,
            'user_id'=>$err->user_id,
            'status'=>Elector::STATUS_ACTIVE
        ));
        $this->assertCount(1, $electors);
    }

    public function testDenyRegistration()
    {
        $userId = 2;
                
        $err = new ElectorRegistrationRequest();
        $err->election_id = 1;
        $err->initiator_id = $userId;
        $err->user_id = $userId;
        $err->save();
        
        $err->status = ElectorRegistrationRequest::STATUS_DECLINED;
        $err->save();
        
        $this->assertEquals(ElectorRegistrationRequest::STATUS_DECLINED, $err->status);
        
        $row = Yii::app()->db->createCommand(
            'SELECT * FROM elector_registration_request WHERE id = ' . $err->id
        )->queryRow(true);
        
        $this->assertEquals($err->getAttributes(), $row);        
        
        $electors = Elector::model()->findAllByAttributes(array(
            'election_id'=>$err->election_id,
            'user_id'=>$err->user_id
        ));
        $this->assertCount(0, $electors);
    }
    
    public function testRegistrationRequestedByUserInElectionWithConfirmationNotNeeded()
    {
        $userId = 2;
                
        $err = new ElectorRegistrationRequest();
        $err->election_id = 2;
        $err->initiator_id = $userId;
        $err->user_id = $userId;
        
        $electors = Elector::model()->findAllByAttributes(array(
            'election_id'=>$err->election_id,
            'user_id'=>$err->user_id
        ));
        $this->assertCount(0, $electors);
        
        $err->save();
        
        $relElector = $err->elector;
        
        $errId = $err->id;
        $this->assertGreaterThan(0, $errId);
        $this->assertEquals($err->status, ElectorRegistrationRequest::STATUS_REGISTERED);
        
        $row = Yii::app()->db->createCommand(
            'SELECT * FROM elector_registration_request WHERE id = ' . $errId
        )->queryRow(true);
        
        $this->assertEquals($err->getAttributes(), $row);
        $electors = Elector::model()->findAllByAttributes(array(
            'election_id'=>$err->election_id,
            'user_id'=>$err->user_id,
            'status'=>Elector::STATUS_ACTIVE
        ));
        $this->assertCount(1, $electors);
        $this->assertEquals($electors[0]->id, $relElector->id);
        $this->assertEquals($electors[0]->user_id, $relElector->user_id);
        $this->assertEquals($electors[0]->election_id, $relElector->election_id);
        $this->assertEquals($electors[0]->status, $relElector->status);
    }
    
    public function testRegistrationAvailabilityCheck()
    {
        $electionId=1;
        $userId=2;
        $this->assertTrue(ElectorRegistrationRequest::isAvailable($electionId, $userId));
        
        $err = new ElectorRegistrationRequest();
        $err->election_id = $electionId;
        $err->initiator_id = $userId;
        $err->user_id = $userId;
        $err->save();
        
        $this->assertFalse(ElectorRegistrationRequest::isAvailable($electionId, $userId));
    }
    
    public function testRegNotCreatedWhenElectionInProcessStatus()
    {
        $electionId=1;
        $userId=2;
        
        Yii::app()->db->createCommand('UPDATE election SET status = ' 
            . Election::STATUS_FINISHED 
            . ' WHERE id = ' . $electionId )->execute();
        
        $this->setExpectedException('CException', 'ElectorRegistrationRequest is unavailable');
        
        $err = new ElectorRegistrationRequest();
        $err->election_id = $electionId;
        $err->initiator_id = $userId;
        $err->user_id = $userId;
        $this->assertEquals(Election::STATUS_FINISHED, $err->election->status);
        $err->save();
        
        $this->assertFalse($err->save());
    }
    
    public function testUserAddsToGroupsAfterConfirmationInElectionWithConfirmationNeeded()
    {
        $userId = 2;
                
        $err = new ElectorRegistrationRequest();
        $err->election_id = $electionId = 3;
        $err->initiator_id = $userId;
        $err->user_id = $userId;
        $err->data = array('groups' => array(1,2));
        $this->assertTrue($err->save());
        
        $groupMembers = VoterGroupMember::model()->findAllByAttributes(array(
            'user_id' => $userId
        ));
        
        $this->assertCount(0, $groupMembers);
        
        $err->status = ElectorRegistrationRequest::STATUS_REGISTERED;
        $this->assertTrue($err->save());
        
        $groupMembers = VoterGroupMember::model()->findAllByAttributes(array(
            'user_id' => $userId
        ));
        $this->assertCount(2, $groupMembers);
        $this->assertInstanceOf(
            VoterGroupMember, 
            VoterGroupMember::model()->findByAttributes(array(
                'user_id' => $userId,
                'voter_group_id' => 1
            ))
        );
        $this->assertInstanceOf(
            VoterGroupMember, 
            VoterGroupMember::model()->findByAttributes(array(
                'user_id' => $userId,
                'voter_group_id' => 2
            ))
        );
    }
}

