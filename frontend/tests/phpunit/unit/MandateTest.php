<?php
class MandateTest extends CDbTestCase
{
    protected $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'mandate'      => array('Mandate', 'functional/petitionsOnMandateDetails/mandate'),
        'candidate'    => array('Candidate', 'unit/electionProcess/candidate')
    );
    
    public function testMandatesForUser()
    {
        $mandates = Mandate::model()->getUsersMandates(1);
        
        $this->assertCount(1, $mandates);
        $this->assertEquals(1, $mandates[0]->id);
        $this->assertEquals('Mandate of Election 1', $mandates[0]->name);
    }
}

