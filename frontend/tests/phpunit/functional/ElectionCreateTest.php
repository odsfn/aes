<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionCreateTest extends WebTestCase
{
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier'
    );    
    
    public function testCreateDefaults() 
    {    
        $this->login("truvazia@gmail.com", "qwerty");
        
        $this->click("link=Elections");
        $this->waitForPageToLoad("30000");
        $this->click("id=a_create_election");
        $this->waitForPageToLoad("30000");
        $this->assertValue('id=Election_voter_reg_type', Election::VOTER_REG_TYPE_ADMIN);
        $this->assertEquals("By Admin", $this->getSelectedLabel("id=Election_voter_reg_type"));

        $this->assertValue('id=Election_voter_reg_confirm', Election::VOTER_REG_CONFIRM_NOTNEED);
        $this->assertEquals("No", $this->getSelectedLabel("id=Election_voter_reg_confirm"));
        
        $this->assertValue('id=Election_cand_reg_type', Election::CAND_REG_TYPE_ADMIN);
        $this->assertEquals("By Admin", $this->getSelectedLabel("id=Election_cand_reg_type"));        
        
        $this->assertValue('id=Election_cand_reg_confirm', Election::CAND_REG_CONFIRM_NEED);
        $this->assertEquals("Yes", $this->getSelectedLabel("id=Election_cand_reg_confirm"));
    }
}
