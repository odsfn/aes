<?php
class NominationsTest extends WebTestCase {

    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'target'       => 'Target',
        'election'     => array('Election', 'functional/electionRegistration/election'),
        'candidate'    => array('Candidate', 'functional/nominations/candidate'),
        'AuthAssignment' => 'AuthAssignment',
        'election_auth_assignment' => ':election_auth_assignment'
    );
    
    public function testNominationByAdmin() 
    {
        //admin opened candidates page
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open("/index-test.php/election/candidates/5");
        $this->waitForPageToLoad();
        
        //see that no any candidates has been added yet 
        $this->sleep(500);
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementNotContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(1) a', 'Steve Jobs');
        
        //invites Steve
        $this->click("link=Invite");
        $this->waitForTextPresent('Steve Jobs');
        $this->click("css=div.user-info:nth-of-type(6) > div.pull-right > span.controls > small");
        $this->sleep(250);
        $this->waitForElementContainsText('css=div.user-info:nth-of-type(6)', 'Invited');
        
        $this->click("link=Vasiliy Pedak");
        $this->click("link=Log out");
        $this->waitForPageToLoad("4000");
        $this->click("link=People");
        $this->waitForPageToLoad("4000");
        $this->click("link=Steve Jobs");
        $this->waitForPageToLoad("4000");
        $this->click("link=Nominations");
        $this->waitForPageToLoad("4000");
        $this->waitForElementPresent('css=div.nomination', 3000);
        $this->assertElementContainsText('css=div.nomination', 'Invited');
        
        $this->click("link=Sign in");
        $this->waitForPageToLoad("4000");
        $this->type("id=LoginForm_identity", "tester5@mail.ru");
        $this->type("id=LoginForm_password", "qwerty");
        $this->click("id=yw0");
        $this->waitForPageToLoad("4000");
        $this->click("link=My nominations");
        $this->waitForPageToLoad("4000");
        $this->waitForElementPresent('css=div.nomination', 3000);
        $this->click("link=Accept");
        $this->waitForTextPresent('Registered');
        $this->assertElementContainsText('css=.nomination-items div.status-registration:nth-of-type(1)', 'Registered');
        
        $this->open("/index-test.php/election/candidates/5");
        $this->waitForPageToLoad();
        
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 2);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(2) a', 'Steve Jobs');
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(2) .body > div:nth-of-type(3)', 'Registered');
    }
    
    public function testNominationByAdminRevokedByAdminBeforeUserAcceptedIt()
    {
        //admin opened candidates page
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open("/index-test.php/election/candidates/5");
        $this->waitForPageToLoad();
        
        //see that no any candidates has been added yet 
        $this->sleep(500);
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementNotContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(1) a', 'Steve Jobs');
        
        //invites Steve
        $this->click("link=Invite");
        $this->waitForTextPresent('Steve Jobs');
        $this->click("css=div.user-info:nth-of-type(6) > div.pull-right > span.controls > small");
        $this->sleep(250);
        $this->waitForElementContainsText('css=div.user-info:nth-of-type(6)', 'Invited');
        
        //open details
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 2);
        $this->click('css=#all-cands-tab .items div.user-info:nth-of-type(2) a');
        $this->sleep(300);
        $this->waitForVisible('css=#candidate-details #controls');
        
        //check that refuse button is present
        $this->assertElementPresent('css=#controls button.refuse-from-reg');
        
        $this->click('css=#controls button.refuse-from-reg');
        $this->waitForElementContainsText('css=div.user-info:nth-of-type(6)', 'Refused');
        
        //No any buttons from current moment
        $this->assertElementNotPresent('css=#controls button');
        
        $this->click('css=ul.breadcrumbs > li:nth-of-type(3) > a.route');
        $this->sleep(300);
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 2);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(2) a', 'Steve Jobs');
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(2)', 'Refused');
        
        $this->open("/index-test.php/election/candidates/5");
        $this->waitForPageToLoad();
        
        //see that no any candidates has been added yet 
        $this->sleep(500);
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 2);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(2) a', 'Steve Jobs');
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(2)', 'Refused');
    }
    
    public function testInvitedUserDeclinesInvitation()
    {
        //admin opened candidates page
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open("/index-test.php/election/candidates/5");
        $this->waitForPageToLoad();
        
        //see that no any candidates has been added yet 
        $this->sleep(500);
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementNotContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(1) a', 'Steve Jobs');
        
        //invites Steve
        $this->click("link=Invite");
        $this->waitForTextPresent('Steve Jobs');
        $this->click("css=div.user-info:nth-of-type(6) > div.pull-right > span.controls > small");
        $this->sleep(250);
        $this->waitForElementContainsText('css=div.user-info:nth-of-type(6)', 'Invited');
        
        $this->logout();
        $this->login('tester5@mail.ru', 'qwerty');
        $this->open("/index-test.php/election/candidates/5/details/10");
        $this->waitForVisible('css=#candidate-details #controls');
        
        //check that refuse and accept button is present
        $this->assertElementPresent('css=#controls button.self-confirm');
        $this->assertElementPresent('css=#controls button.self-refuse');
        
        $this->click('css=#controls button.self-refuse');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(3)', 'Refused');
        $this->sleep(250);
        $this->assertElementNotPresent('css=#controls button');
        
        $this->open("/index-test.php/election/candidates/5");
        $this->waitForPageToLoad();
        
        //see that no any candidates has been added yet 
        $this->sleep(500);
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 2);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(2) a', 'Steve Jobs');
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(2)', 'Refused');        
    }
            
    public function testAdminCanBlockCandidate()
    {
        $elecion = Election::model()->findByPk(5);
        $elecion->status = Election::STATUS_ELECTION;
        $elecion->save(false);
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('/index-test.php/election/candidates/5/details/8');
        $this->waitForPageToLoad();
        $this->waitForVisible('css=#candidate-details #controls');
        
        $this->waitForCssCount('css=#controls button', 1);
        $this->click('css=#controls button.block');
        
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(3)', 'Refused');
        $this->sleep(250);
        $this->assertElementNotPresent('css=#controls button');
        
        //check nominations
        $this->logout();
        $this->login('tester1@mail.ru', 'qwerty');
        $this->click("link=My nominations");
        $this->waitForPageToLoad();
                
        $this->waitForCssCount('css=#nominations-feed-container .items > div', 2);
        $this->assertElementContainsText('css=#nominations-feed-container .items > div:nth-of-type(2) h4', 'Election 5');
        $this->assertElementContainsText('css=#nominations-feed-container .items > div:nth-of-type(2) .status', 'Blocked');
    }
    
    public function testAuthorizedDontSeeAnyButtonsOnDetailsOfAnother()
    {
        $this->login("tester5@mail.ru", 'qwerty');
        $this->open('/index-test.php/election/candidates/5/details/8');
        $this->waitForPageToLoad();
        $this->waitForVisible('css=#candidate-details #controls');
        $this->sleep(250);
        $this->assertElementNotPresent('css=#controls button');
    }
    
    public function testSelfNominationDecline()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        $this->waitForPageToLoad("4000");
        $this->click("link=My nominations");
        $this->waitForPageToLoad("4000");
        $this->waitForElementPresent('css=div.nomination', 3000);
        $this->assertCssCount('css=div.nomination',2);
        $this->assertElementContainsText('css=div.nomination:nth-of-type(1) .status', 'Awaiting registration confirmation');
        $this->assertNotVisible('css=div.nomination:nth-of-type(1) .accept-btn');
        
        $this->click('css=div.nomination:nth-of-type(1) .decline-btn');
        $this->waitForElementContainsText('css=div.nomination:nth-of-type(1) .status', 'Refused');
    }
    
    //  testNominationByAdminRevokedByAdminAfterUserAcceptedIt
    //  testRegisteredCandidateRefusesFromParticipation    
}
