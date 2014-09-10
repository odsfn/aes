<?php

class VoterGroupMembersRegistrationTest extends CDbTestCase 
{
    public $fixtures = array(
        'election' => array('Election', 'unit/voterGroupMembersRegistration/election'),
        'elector'  => array('Elector', 'unit/voterGroupMembersRegistration/elector'),
        'voter_group' => array('VoterGroup', 'unit/voterGroupMembersRegistration/voter_group'),
        'voter_group_member' => array('VoterGroupMember', 'unit/voterGroupMembersRegistration/voter_group_member'),
        'election_voter_group' => array('ElectionVoterGroup', 'unit/voterGroupMembersRegistration/election_voter_group')
    );
    
    public function testRegistration()
    {
        $election = Election::model()->findByPk(1);
        $el2 = Election::model()->findByPk(2);
        $el2StartElectors = array(2,3,4);
        
        $this->checkUsersAreElectors(array('1', '2'), $election->id);
        $this->checkUsersAreElectors($el2StartElectors, $el2->id);
        
        $reg = new VoterGroupMembersRegistration($election);
        $reg->run();
        
        $this->checkUsersAreElectors(array('1', '2', '3', '4', '5'), $election->id);
        $this->checkUsersAreElectors($el2StartElectors, $el2->id);
        
        $reg = new VoterGroupMembersRegistration($el2);
        $reg->run();
        
        $this->checkUsersAreElectors(array('1', '2', '3', '4', '5'), $election->id);
        $this->checkUsersAreElectors(array(2,3,4,5,6), $el2->id);
    }
    
    protected function checkUsersAreElectors($usersIds, $electionId)
    {
        $electors = Yii::app()->db->createCommand(
                "SELECT user_id FROM elector WHERE election_id = $electionId"
        )->queryColumn();
                
        $this->assertCount(count($usersIds), $electors);
        foreach ($usersIds as $id)
            $this->assertContains($id, $electors);
    }
}

