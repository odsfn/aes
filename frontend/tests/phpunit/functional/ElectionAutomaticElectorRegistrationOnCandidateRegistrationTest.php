<?php

class ElectionAutomaticElectorRegistrationOnCandidateRegistrationTest extends WebTestCase 
{
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_profile' => 'userAccount.models.Profile',
        'election' => array('Election', 'functional/autoElectorRegistration/election'),
        'elector' => 'Elector',
        'candidate' => 'Candidate',
        'AuthAssignment' => array('AuthAssignment', 'functional/autoElectorRegistration/AuthAssignment'),
        'election_auth_assignment' => array('ElectionAuthAssignment', 'functional/autoElectorRegistration/election_auth_assignment')
    );
    
    /**
     * @dataProvider electionsWithoutVoterGroupsRestrictions
     */
    public function testAutomaticRegistration($electionId)
    {
        $this->login('tester1@mail.ru', 'qwerty');
            
        $this->open('election/view/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForPresent($regBtnSel = 'css=button#register-candidate');
        $this->assertVisible($regBtnSel);
        
//        $this->waitForPresent($regElectorBtnSel = 'css=button#register-elector');
//        $this->assertVisible($regElectorBtnSel);
        
        $this->click($regBtnSel);
        $this->waitForTextPresent('You have been registered as candidate');
//        $this->waitForTextPresent('You have been registered as candidate and elector');
        
//        $this->waitForElementNotPresent($regElectorBtnSel);

        $this->open('election/electorate/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForElementPresent($electorsContSel = 'css=#dest-tab.active div.items');
        $this->waitForCssCount($electorsContSel .= ' > div.user-info', 1);
        $this->assertElementContainsText($electorsContSel, 'Another User');
    }
    
    public function testAutomaticRegistrationAfterConfirmationByAdmin()
    {
        $electionId = 4;
        
        $this->login('tester1@mail.ru', 'qwerty');
            
        $this->open('election/view/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForPresent($regBtnSel = 'css=button#register-candidate');
        $this->assertVisible($regBtnSel);
        
        $this->click($regBtnSel);
        $this->waitForTextPresent('Your registration request was sent. Election manager will consider it as soon as possible');
        
        $this->open('election/electorate/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForElementPresent($electorsContSel = 'css=#dest-tab.active div.items');
        $this->assertElementNotContainsText($electorsContSel, 'Another User');
        
        $this->logout();
        //Admin confirms
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('election/candidates/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForElementPresent($electorsContSel = 'css=#all-cands-tab.active div.items');
        $this->waitForCssCount($electorsContSel . ' > div.user-info', 1);
        $this->click($electorsContSel . ' > div.user-info .body a.route');
        
        $this->waitForElementPresent($allowBtn = 'css=#candidate-details #controls button.confirm');
        $this->assertVisible($allowBtn);
        $this->click($allowBtn);
        
        $this->waitForElementNotPresent($allowBtn);
        $this->sleep(300);
        
        $this->open('election/electorate/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForElementPresent($electorsContSel = 'css=#dest-tab.active div.items');
        $this->waitForCssCount($electorsContSel .= ' > div.user-info', 1);
        $this->assertElementContainsText($electorsContSel, 'Another User');
    }
    
    public function testAutomaticRegistrationAfterConfirmationByCandidate()
    {
        $electionId = 5;
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('election/candidates/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForElementPresent($electorsContSel = 'css=#all-cands-tab.active div.items');
        $this->waitForCssCount($electorsContSel . ' > div.user-info', 0);
        
        $this->click('css=li#invite-tab-sel a');
        
        $this->waitForVisible('css=div#invite-tab.active');
        $this->waitForCssCount('css=#invite-tab .items div.user-info', 6);
        $this->click('css=#invite-tab .items div.user-info:nth-of-type(2) .controls i.icon-plus-sign');
        $this->waitForElementPresent('css=#invite-tab .items div.user-info:nth-of-type(2) .mark i.icon-ok');
        
        $this->logout();
        
        //Candidate confirms
        $this->login('tester1@mail.ru', 'qwerty');
        
        $this->open('election/electorate/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForElementPresent($electorsContSel = 'css=#dest-tab.active div.items');
        $this->assertElementNotContainsText($electorsContSel, 'Another User');
        
        $this->open('userPage/nominations/2');
        $this->waitForPageToLoad();
        $this->waitForTextPresent('Election 5');
        
        $this->click('css=div.nomination-items .status-registration a.accept-btn');
        $this->waitForElementContainsText('css=div.nomination-items .status-registration' , 'Registered');
        
        $this->open('election/electorate/' . $electionId);
        $this->waitForPageToLoad();
        $this->waitForElementPresent($electorsContSel = 'css=#dest-tab.active div.items');
        $this->waitForCssCount($electorsContSel .= ' > div.user-info', 1);
        $this->assertElementContainsText($electorsContSel, 'Another User');
    }    
    
    public function electionsWithoutVoterGroupsRestrictions()
    {
        return array(
            array(1), 
            array(2), 
            array(3)
        );
    }
}