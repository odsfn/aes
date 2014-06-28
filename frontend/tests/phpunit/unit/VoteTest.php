<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class VoteTest extends CDbTestCase 
{
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'target'       => 'Target',
        'election'     => array('Election', 'functional/electionProcess/election'),
        'mandate'      => 'Mandate',
        'candidate'    => array('Candidate', 'unit/electionProcess/candidate'),
        'elector'    => array('Elector', 'unit/electionProcess/elector'),
        'vote'       => array('Vote', 'functional/electionProcess/vote'),
        'AuthAssignment' => array('AuthAssignment', 'functional/electionProcess/AuthAssignment'),
        'election_auth_assignment' => array(':election_auth_assignment', 'functional/electionProcess/election_auth_assignment')
    );    
    
    public function testSaveFailsOnRevotesLimit()
    {
        $vote = new Vote;
        $vote->election_id = 1;
        $vote->candidate_id = 3;
        $vote->user_id = 1;
        $this->assertTrue($vote->save());
        
        //first revote
        $vote->status = Vote::STATUS_REVOKED;
        $this->assertTrue($vote->save());
        
        $vote = new Vote;
        $vote->candidate_id = 3;
        $vote->user_id = 1;
        $vote->election_id = 1;
        $this->assertTrue($vote->save());
        
        //second
        $vote->status = Vote::STATUS_REVOKED;
        $this->assertTrue($vote->save());
        
        $vote = new Vote;
        $vote->candidate_id = 4;
        $vote->user_id = 1;
        $vote->election_id = 1;
        $this->assertTrue($vote->save());
        
        //third
        $vote->status = Vote::STATUS_REVOKED;
        $this->assertTrue($vote->save());
        
        $vote = new Vote;
        $vote->candidate_id = 5;
        $vote->user_id = 1;
        $vote->election_id = 1;
        $this->assertTrue($vote->save());
        
        //fourth is unsuccessful
        $this->setExpectedException('Exception', 'Revote limit has been reached');
        $vote->status = Vote::STATUS_REVOKED;
        $this->assertFalse($vote->save());
        
        $this->setExpectedException('Exception', 'Revote limit has been reached');
        $vote = new Vote;
        $vote->candidate_id = 3;
        $vote->user_id = 1;
        $vote->election_id = 1;
        $this->assertFalse($vote->save());
    }
    
    public function testSaveFailsOnRevokeTimeout()
    {
        $vote = new Vote;
        $vote->election_id = 1;
        $vote->candidate_id = 3;
        $vote->user_id = 1;
        $this->assertTrue($vote->save());
        
        //Simulating that timer is expired
        $voted = new DateTime($vote->date);
        $voted->sub(new DateInterval('PT'. $vote->election->remove_vote_time .'M'));
        $voted = $voted->format('Y-m-d H:i:s');
        Yii::app()->db->createCommand()->update('vote', array('date' => $voted), 'id = ' . $vote->id);
        
        $this->setExpectedException('Exception', 'Revoke vote timeout has been reached');
        $vote->status = Vote::STATUS_REVOKED;
        $this->assertFalse($vote->save());
    }
    
    public function testPassVoteFailsBecauseOfTimeout()
    {
        $vote = new Vote;
        $vote->election_id = 1;
        $vote->candidate_id = 3;
        $vote->user_id = 1;
        $this->assertTrue($vote->save());
        
        $vote->status = Vote::STATUS_REVOKED;
        $this->assertTrue($vote->save());
        
        //Simulating that timer is expired
        $voted = new DateTime($vote->date);
        $voted->sub(new DateInterval('PT'. $vote->election->revote_time .'M'));
        $voted = $voted->format('Y-m-d H:i:s');
        Yii::app()->db->createCommand()->update('vote', array('date' => $voted), 'id = ' . $vote->id);
        
        $this->setExpectedException('Exception', 'Revote timeout has been reached');
        $vote = new Vote;
        $vote->election_id = 1;
        $vote->candidate_id = 4;
        $vote->user_id = 1;
        $this->assertFalse($vote->save());        
    }

    public function testFailsOnTryToPassVoteWhenAnotherWerePassed()
    {
        $vote = new Vote;
        $vote->election_id = 1;
        $vote->candidate_id = 3;
        $vote->user_id = 1;
        $this->assertTrue($vote->save());
        
        $this->setExpectedException('Exception', 'Vote has already been passed');
        $vote = new Vote;
        $vote->election_id = 1;
        $vote->candidate_id = 4;
        $vote->user_id = 1;
        $this->assertFalse($vote->save());
    }
}