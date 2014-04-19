<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionProcessTest extends CDbTestCase {
    
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'target'       => 'Target',
        'election'     => 'Election',
        'candidate'    => array('Candidate', 'unit/electionProcess/candidate'),
        'elector'    => array('Elector', 'unit/electionProcess/elector'),
        'vote'       => array('Vote')
    );

    protected $electionFinishedStateMock = null;
    
    protected function setUp() {
        parent::setUp();
        
        $this->electionFinishedStateMock = null;
    }

    protected function getPreparedElection($methods) {
        $electionMock = new Election();
        
        $electionMock->detachBehavior('statusState');
        
        $statusStateConf = $electionMock->behaviors();
        $statusStateConf = $statusStateConf['statusState'];
        
        $statesConf = $statusStateConf['states'];
        
        unset($statusStateConf['states']);
        
        $behaviorMock = Yii::createComponent($statusStateConf);
        $electFinishedStateConf = $statesConf[3];
        
        
        $this->electionFinishedStateMock = $this->getMock('ElectionFinishedState', 
                $methods,
                array(
                    'name'  => $electFinishedStateConf['name'], 
                    'owner' => $behaviorMock
                )
        );
        
        unset($electFinishedStateConf['class']);
        
        foreach ($electFinishedStateConf as $name => $value)
            $this->electionFinishedStateMock->$name = $value;
        
        $statesConf[3] = $this->electionFinishedStateMock;
        
        $behaviorMock->setStates($statesConf);
        
        $electionMock->attachBehavior('statusState', $behaviorMock);
        
        return $electionMock;
    }
    
    public function testCallsFinishMethod() {
        
        $electionMock = $this->getPreparedElection(array('finish'));
        
        $this->electionFinishedStateMock->expects($this->once())
            ->method('finish');
        
        $electionMock->name = "Some election";
        $electionMock->mandate = "Mandate of some election";
        $electionMock->quote = 1000;
        $electionMock->validity = 12;
        $electionMock->user_id = 1;
        $this->assertTrue($electionMock->save(), 'Election can\'t be saved');
        
        $this->assertEquals('Published', $electionMock->statusState->stateName);
        
        $this->assertFalse($electionMock->canTransit('Finished'));
        $this->assertFalse($electionMock->canTransit('Election'));
        $this->assertTrue($electionMock->canTransit('Registration'));
        
        $electionMock->status = Election::STATUS_REGISTRATION;
        
        $this->assertTrue($electionMock->save(), 'Election can\'t be saved');
        $this->assertEquals('Registration', $electionMock->statusState->stateName);
        
        $this->assertFalse($electionMock->canTransit('Finished'));
        $this->assertTrue($electionMock->canTransit('Election'));
        $this->assertTrue($electionMock->canTransit('Published'));
        
        $electionMock->status = Election::STATUS_ELECTION;
        $this->assertTrue($electionMock->save(), 'Election can\'t be saved');
        
        $electionMock->status = Election::STATUS_FINISHED;
        $this->assertTrue($electionMock->save(), 'Election can\'t be saved');
        
        $electionMock = Election::model()->findByPk($electionMock->id);
        $electionMock->name = "Some election. Edited";
        $electionMock->save();
    }    
    
//    public function testValidationStatus() {
//        $this->markTestIncomplete();
//    }
    
    public function testCreateMandateMethod() {
        $candidate = $this->getFixtureManager()->getRecord('candidate', 3);
        $electedCandidateId = $candidate->id;
        
        $election = Election::model()->findByPk($candidate->election_id);
        $election->quote = 1;
        $election->status = Election::STATUS_ELECTION;
        $this->assertTrue($election->save());
        
        $vote = new Vote;
        $vote->candidate_id = $electedCandidateId;
        $vote->election_id = $candidate->election_id;
        $vote->user_id = $election->electors[0]->user_id;
        $this->assertTrue($vote->save(), print_r($vote->getErrors(), true));
        
        $election->status = Election::STATUS_FINISHED;
        $election->save();
        
        $mandate = Mandate::model()->findByAttributes(
                array(
                    'election_id'=>$candidate->election_id,
                    'candidate_id'=>$candidate->id
                )
        );
        
        $this->assertEquals($election->mandate, $mandate->name);
        $this->assertEquals($election->validity, $mandate->validity);
    }
    
    public function testNotCreateMandateWhenVotesCountLessThanQuote() {
        $candidate = $this->getFixtureManager()->getRecord('candidate', 3);
        $electedCandidateId = $candidate->id;
        
        $electionMock = $this->getPreparedElection(array('createMandate'));
        
        $electionMock->isNewRecord = false;
        $electionMock->id = $candidate->election_id;
        $electionMock->refresh();
        $electionMock->quote = 2;
        $electionMock->status = Election::STATUS_ELECTION;
        $this->assertTrue($electionMock->save());
        
        $vote = new Vote;
        $vote->candidate_id = $electedCandidateId;
        $vote->election_id = $candidate->election_id;
        $vote->user_id = $electionMock->electors[0]->user_id;
        $this->assertTrue($vote->save(), print_r($vote->getErrors(), true));
        
        $this->electionFinishedStateMock->expects($this->never())
            ->method('createMandate');
        
        $electionMock->status = Election::STATUS_FINISHED;
        $electionMock->save();
    }
    
    public function testCreateMandateForCandidatesWithVotesCountGreaterThanQuote() {
        $candidates = $this->getFixtureManager()->getRows('candidate');
        
        $electionMock = $this->getPreparedElection(array('createMandate'));
        
        $electionMock->isNewRecord = false;
        $electionMock->id = $candidates[3]['election_id'];
        $electionMock->refresh();
        $electionMock->quote = 2;
        
        $this->assertEquals(Election::STATUS_PUBLISHED, $electionMock->status);
        $this->assertEquals('Published', $electionMock->statusState->stateName);
        
        $electionMock->status = Election::STATUS_REGISTRATION;
        $this->assertTrue($electionMock->save());
        
        $electionMock->status = Election::STATUS_ELECTION;
        $this->assertTrue($electionMock->save());
        
        $this->assertEquals(Election::STATUS_ELECTION, $electionMock->status);
        $this->assertEquals('Election', $electionMock->statusState->stateName);        
        
        $vote = new Vote;
        $vote->candidate_id = $candidates[3]['id'];
        $vote->election_id = $candidates[3]['election_id'];
        $vote->user_id = $electionMock->electors[0]->user_id;
        $this->assertTrue($vote->save(), print_r($vote->getErrors(), true));
        
        $vote = new Vote;
        $vote->candidate_id = $candidates[3]['id'];
        $vote->election_id = $candidates[3]['election_id'];
        $vote->user_id = $electionMock->electors[1]->user_id;
        $this->assertTrue($vote->save(), print_r($vote->getErrors(), true));        
        
        $vote = new Vote;
        $vote->candidate_id = $candidates[4]['id'];
        $vote->election_id = $candidates[3]['election_id'];
        $vote->user_id = $electionMock->electors[2]->user_id;
        $this->assertTrue($vote->save(), print_r($vote->getErrors(), true));
        
        $vote = new Vote;
        $vote->candidate_id = $candidates[4]['id'];
        $vote->election_id = $candidates[3]['election_id'];
        $vote->user_id = $electionMock->electors[3]->user_id;
        $this->assertTrue($vote->save(), print_r($vote->getErrors(), true));
        
        $vote = new Vote;
        $vote->candidate_id = $candidates[5]['id'];
        $vote->election_id = $candidates[3]['election_id'];
        $vote->user_id = $electionMock->electors[4]->user_id;
        $this->assertTrue($vote->save(), print_r($vote->getErrors(), true));        
        
        $this->electionFinishedStateMock->expects($this->exactly(2))
                ->method('createMandate');
        
        $this->electionFinishedStateMock->expects($this->at(0))
            ->method('createMandate')
            ->with($this->callback(
                    function($electedCandidate) use(&$candidates) {
                        return $electedCandidate->id == $candidates[3]['id'];
                    }
                   )
             );
        
        $this->electionFinishedStateMock->expects($this->at(1))
            ->method('createMandate')
            ->with($this->callback(
                    function($electedCandidate) use(&$candidates) {
                        return $electedCandidate->id == $candidates[4]['id'];
                    }
                   )
             );
        
        $this->assertEquals(Election::STATUS_ELECTION, $electionMock->status);
        $this->assertEquals('Election', $electionMock->statusState->stateName);           
        $this->assertTrue($electionMock->canTransit('Finished'));
        $electionMock->status = Election::STATUS_FINISHED;
        $this->assertTrue($electionMock->save(), print_r($electionMock->getErrors(), true));     
    }
}
