<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionProcessTest extends WebTestCase {
    
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'target'       => 'Target',
        'election'     => 'Election',
        'mandate'      => 'Mandate',
        'candidate'    => array('Candidate', 'unit/electionProcess/candidate'),
        'elector'    => array('Elector', 'unit/electionProcess/elector'),
        'vote'       => array('Vote'),
        'AuthAssignment' => 'AuthAssignment',
        'election_auth_assignment' => ':election_auth_assignment'
    );    
    
    public function testProcess() {
        
        $this->login("truvazia@gmail.com", "qwerty");
        
        $this->click("link=Elections");
        $this->waitForPageToLoad("30000");
        $this->click("id=a_create_election");
        $this->waitForPageToLoad("30000");
        $this->type("id=Election_name", "Election 3");
        $this->type("id=Election_mandate", "Mandate of Election 3");
        $this->type("id=Election_quote", "1");
        $this->type("id=Election_validity", "24");
        $this->click("id=yw0");
        $this->waitForPageToLoad("30000");
        $this->click("link=×");
        
        $this->assertTextPresent('Election 3');
        $this->assertElementContainsText('css=div#title.bootstrap-widget div.bootstrap-widget-header h3.pull-right small', 'Published');
        
        $this->click("link=Management");
        $this->waitForPageToLoad("3000");
        $this->checkSelectOptions('Canceled, Registration, Published', 'id=Election_status');
        $this->select("id=Election_status", "label=Registration");
        $this->click("id=yw0");
        $this->waitForPageToLoad("30000");
        $this->click("link=×");
        
        $this->assertTextPresent('Election 3');
        $this->assertElementContainsText('css=div#title.bootstrap-widget div.bootstrap-widget-header h3.pull-right small', 'Registration');
        $this->assertValue('id=Election_validity', 24);
        $this->checkSelectOptions('Canceled, Published, Election, Registration', 'id=Election_status');
        
        
        $this->type("id=Election_validity", "12");
        $this->click("id=yw0");
        $this->waitForPageToLoad("30000");
        $this->click("link=×");

        $this->assertTextPresent('Election 3');
        $this->assertElementContainsText('css=div#title.bootstrap-widget div.bootstrap-widget-header h3.pull-right small', 'Registration');
        $this->assertValue('id=Election_validity', 12);
        $this->checkSelectOptions('Canceled, Published, Election, Registration', 'id=Election_status');
        
        $this->click("link=Candidates");
        $this->waitForPageToLoad("3000");
        $this->waitForElementPresent("link=Invite");
        $this->click("link=Invite");
        
        $this->waitForElementPresent("css=div.user-info");
        $this->click("css=div.user-info:nth-of-type(1) > div.pull-right > span.controls > small");
        $this->click("css=div.user-info:nth-of-type(2) > div.pull-right > span.controls > small");
        
        $this->click("link=Electorate");
        $this->waitForPageToLoad("3000");
        $this->click("link=Invite");
        $this->waitForElementPresent("css=div.user-info");
        $this->click("css=div.user-info:nth-of-type(1) > div.pull-right.right-top-panel > span.controls > small");
        $this->click("css=div.user-info:nth-of-type(2) > div.pull-right.right-top-panel > span.controls > small");
        $this->click("css=div.user-info:nth-of-type(3) > div.pull-right.right-top-panel > span.controls > small");
        $this->click("css=div.user-info:nth-of-type(4) > div.pull-right.right-top-panel > span.controls > small");
        $this->click("css=div.user-info:nth-of-type(6) > div.pull-right.right-top-panel > span.controls > small");

        $this->click("link=Your page");
        $this->waitForPageToLoad("3000");
        $this->click("link=My nominations");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Election 3');
        $acceptBtn = 'css=div.nomination-items div.status-registration div.nomination span.controls a.text-success';
        $this->waitForElementPresent($acceptBtn);
        $this->click($acceptBtn);

        $this->click("link=Elections");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Election 3');
        $this->click("link=Election 3");
        $this->waitForPageToLoad("3000");
        
        $this->click("link=Management");
        $this->waitForPageToLoad("3000");
        $this->select("id=Election_status", "label=Election");
        $this->click("id=yw0");
        $this->waitForPageToLoad("30000");
        $this->click("link=×");
        
        $this->assertElementContainsText('css=div#title.bootstrap-widget div.bootstrap-widget-header h3.pull-right small', 'Election');
        $this->checkSelectOptions('Canceled, Finished, Election', 'id=Election_status');
        
        $this->click("link=Candidates");
        $this->waitForPageToLoad("3000");
        $voteBox = "css=div.checkbox.vote";
        $this->waitForElementPresent($voteBox);
        $this->click($voteBox);
        
        $this->click("link=Management");
        $this->waitForPageToLoad("3000");
        $this->select("id=Election_status", "label=Finished");
        $this->click("id=yw0");
        $this->waitForPageToLoad("3000");
        $this->click("link=×");
        
        $this->assertElementContainsText('css=div#title.bootstrap-widget div.bootstrap-widget-header h3.pull-right small', 'Finished');
        $this->checkSelectOptions('Finished', 'id=Election_status');
        
        $this->click("link=Mandates");
        $this->waitForPageToLoad("30000");
        $this->waitForTextPresent('Mandate of Election 3');
        
        $this->click("link=Your page");
        $this->waitForPageToLoad("3000");
        $this->click("link=My mandates");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Mandate of Election 3');
        $this->assertCssCount('css=#mandates-feed-container .mandate', 1);
        
        $this->click("link=Elections");
        $this->waitForPageToLoad("3000");
        $this->waitForTextPresent('Election 3');
        $this->click("link=Election 3");
        $this->waitForPageToLoad("3000");
        
        $this->click("link=Candidates");
        $this->waitForPageToLoad("3000");
        $firstCandSel = 'css=div.user-info';
        $this->waitForElementPresent($firstCandSel);
        
        $this->assertElementContainsText($firstCandSel . ' .body a.route', 'Vasiliy Pedak');
        $this->click($firstCandSel . ' .body a.route');
        
        $this->waitForElementPresent("link=Mandate");
        $this->click("link=Mandate");
        
        $this->waitForElementPresent('css=#candidate-details #mandates-tab div ul li a');
        $this->assertElementContainsText('css=#candidate-details #mandates-tab div ul li a', 'Mandate of Election 3');
        
    }
 }
