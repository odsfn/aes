<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionRegistrationTest extends WebTestCase {

    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'election'     => 'Election',
        'elector'      => 'Elector',
        'AuthAssignment' => 'AuthAssignment',
        'election_auth_assignment' => ':election_auth_assignment'
    );
    
    protected function setUp() {
        parent::setUp();
        
        $election = Election::model()->findByPk(1);
        $election->status = Election::STATUS_REGISTRATION;
        $election->save(false);
    }
    
    public function testNotAuthorizedCantRegister() {
        $this->open('election/electorate/1');
        $this->assertFalse($this->checkRegistrationAvailability());
    }
    
    public function testAuthorizedNotAdminCantRegister() {
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('election/electorate/1');
        $this->assertFalse($this->checkRegistrationAvailability());        
    }
    
    public function testAdminCanRegister() {
        $this->loginAsAdmin();
        $this->open('election/electorate/1');
        $this->assertTrue($this->checkRegistrationAvailability());        
    }
    
//    public function testCanFind() {
//        $this->openTestElection();
//    }

    public function testCanRegisterAndRemove() {
        $this->openTestElection();
        
        $this->assertElementNotPresent('css=#dest-tab div.items .user-info');
        
        $firstUserSel = 'css=#source-tab div.items .user-info:nth-of-type(1)';
        $firstUserAddBtnSel = $firstUserSel . ' .controls .icon-plus-sign';
        
        $this->click('css=a[href="#source-tab"]');
        
        $this->waitForElementPresent($firstUserSel);
        
        $this->mouseOver($firstUserSel);
        $this->assertVisible($firstUserAddBtnSel);
        $this->click($firstUserAddBtnSel);
        
        $this->waitForElementNotPresent($firstUserAddBtnSel);
        
        $this->click('css=a[href="#dest-tab"]');
        
        $this->assertElementPresent('css=#dest-tab .items .user-info');
        
        $addedElectorSel = 'css=#dest-tab div.items .user-info:nth-of-type(1)';
        $addedElectorRemoveBtnSel = $addedElectorSel . ' .controls .icon-minus-sign';
        
        $this->mouseOver($addedElectorSel);
        $this->assertVisible($addedElectorRemoveBtnSel);
        $this->click($addedElectorRemoveBtnSel);
        
        $this->waitForElementNotPresent($addedElectorSel);
        
        $this->click('css=a[href="#source-tab"]');
        $this->mouseOver($firstUserSel);
        $this->assertVisible($firstUserAddBtnSel);
    }
  
    public function testAdminCantRegisterWithNotRegisterStatus() {
        $this->loginAsAdmin();
        
        $statuses = array(
            Election::STATUS_PUBLISHED,
            Election::STATUS_ELECTION,
            Election::STATUS_FINISHED,
            Election::STATUS_CANCELED
        );
        
        $election = Election::model()->findByPk(1);
        
        foreach ($statuses as $curStatus) {
            $election->status = $curStatus;
            $election->save(false);        

            $this->open('election/electorate/1');
            $this->assertFalse($this->checkRegistrationAvailability());        
        }
    }
    
    protected function checkRegistrationAvailability() {
        return ($this->isVisible('css=#source-tab-sel') && $this->isElementPresent('css=#source-tab div.items'));
    }
    
    protected function loginAsAdmin() {
        $this->login('vptester@mail.ru', 'qwerty');
    }
    
    protected function openTestElection($login = true) {
        if($login)
            $this->loginAsAdmin();
        
        $this->open('election/electorate/1');
    }
}

