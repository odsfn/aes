<?php
class NominationsTest extends WebTestCase {

    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'target'       => 'Target',
        'election'     => 'Election',
        'candidate'    => 'Candidate',
        'AuthAssignment' => 'AuthAssignment',
        'election_auth_assignment' => ':election_auth_assignment'
    );
    
    public function testNomination() {
        
        $this->open("/index-test.php");
        $this->click("link=Sign in");
        $this->waitForPageToLoad("4000");
        $this->type("id=LoginForm_identity", "truvazia@gmail.com");
        $this->type("id=LoginForm_password", "qwerty");
        $this->click("id=yw0");
        $this->waitForPageToLoad("4000");
        $this->click("link=Elections");
        $this->waitForPageToLoad("4000");
        
        $this->open("/index-test.php/election/management/2");
        $this->waitForPageToLoad("4000");
        
        $this->select("id=Election_status", "label=Registration");
        $this->click("id=yw0");
        $this->waitForPageToLoad("4000");
        $this->click("link=Candidates");
        $this->waitForPageToLoad("4000");
        $this->click("link=Invite");
        $this->waitForTextPresent('Steve Jobs');
        $this->click("css=div.user-info:nth-of-type(6) > div.pull-right > span.controls > small");
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
        $this->click("link=Decline");
        
        $this->waitForNotVisible('css=div.nomination .controls', 3000);
        $this->assertElementContainsText('css=div.nomination', 'Refused');
        
        $this->click("link=Log out");
        $this->waitForPageToLoad("4000");
        
        $this->type("id=LoginForm_identity", "tester1@mail.ru");
        $this->type("id=LoginForm_password", "qwerty");
        $this->click("id=yw0");
        $this->waitForPageToLoad("3000");
        $this->open("/index-test.php/election/management/1");
        $this->waitForPageToLoad("3000");
        $this->waitForElementPresent("id=Election_status", 3000);
        $this->select("id=Election_status", "label=Registration");
        $this->click("id=yw0");
        $this->waitForPageToLoad("3000");
        $this->click("link=Candidates");
        $this->waitForPageToLoad("3000");
        $this->waitForElementPresent('css=div.user-info');
        $this->click("link=Invite");
        $this->click("css=div.user-info:nth-of-type(6) > div.pull-right > span.controls > small");
        
        $this->click("css=ul.breadcrumbs.breadcrumb > li > a");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Election 2');
        $this->click("link=Election 2");
        $this->waitForPageToLoad("3000");
        $this->click("link=Candidates");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Steve Jobs', '300');
        $this->click("link=Steve Jobs");
        $this->click("css=button.btn.invite");
        $this->click("link=People");
        $this->waitForPageToLoad("3000");
        $this->click("link=Steve Jobs");
        $this->waitForPageToLoad("3000");
        $this->click("link=Nominations");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Election 1');
        $this->assertTextPresent('Election 2');
        $this->type("name=name", "2");
        $this->click("css=.btn.form-submit");
        $this->assertTextNotPresent('Election 1');
        $this->click("css=.btn.form-reset");
        $this->waitForTextPresent('Election 1');
        $this->assertTextPresent('Election 2');
        
        $this->click("link=Another User");
        $this->click("link=Log out");
        $this->waitForPageToLoad("3000");
        
        $this->type("id=LoginForm_identity", "tester5@mail.ru");
        $this->type("id=LoginForm_password", "qwerty");
        $this->click("id=yw0");
        $this->waitForPageToLoad("3000");
        $this->click("link=My nominations");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Election 1');
        $this->click("link=Accept");
        $this->waitForTextPresent('Registered');
        $this->assertElementContainsText('css=.nomination-items div.status-registration:nth-of-type(1)', 'Registered');
        $this->assertVisible('css=.nomination-items div.status-registration:nth-of-type(1) .decline-btn');
        $this->assertVisible('css=.nomination-items div.status-registration:nth-of-type(2) .accept-btn');
        $this->assertVisible('css=.nomination-items div.status-registration:nth-of-type(2) .decline-btn');
        
        $this->click("link=Steve Jobs");
        $this->click("link=Log out");
        $this->waitForPageToLoad("3000");
        $this->type("id=LoginForm_identity", "tester1@mail.ru");
        $this->type("id=LoginForm_password", "qwerty");
        $this->click("id=yw0");
        $this->waitForPageToLoad("3000");
        $this->click("link=Elections");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Election 2', '300');
        $this->click("link=Election 2");
        $this->waitForPageToLoad("3000");
        $this->click("link=Management");
        $this->waitForPageToLoad("3000");
        $this->select("id=Election_status", "label=Election");
        $this->click("id=yw0");
        $this->waitForPageToLoad("3000");
        $this->click("link=Another User");
        $this->click("link=Log out");
        $this->waitForPageToLoad("3000");
        $this->type("id=LoginForm_identity", "tester5@mail.ru");
        $this->type("id=LoginForm_password", "qwerty");
        $this->click("id=yw0");
        $this->waitForPageToLoad("3000");
        $this->click("link=My nominations");
        $this->waitForPageToLoad("3000");
        
        $this->waitForTextPresent('Election 2');
        $this->assertElementNotPresent('css=.nomination-items div.status-registration:nth-of-type(2) .accept-btn');
        $this->assertElementNotPresent('css=.nomination-items div.status-registration:nth-of-type(2) .decline-btn');
        $this->assertVisible('css=.nomination-items div.status-registration:nth-of-type(1) .decline-btn');
    }
}
