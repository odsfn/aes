<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionProcessTest extends WebTestCase {
    
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
        
        //Check voting
        $this->click("link=Candidates");
        $this->waitForPageToLoad("3000");
        $voteBox = "css=div.checkbox.vote";
        $this->waitForElementPresent($voteBox);
        $this->click($voteBox);
        
        //Finish process
        $this->click("link=Management");
        $this->waitForPageToLoad("3000");
        $this->select("id=Election_status", "label=Finished");
        $this->click("id=yw0");
        $this->waitForPageToLoad("3000");
        $this->click("link=×");
        
        $this->assertElementContainsText('css=div#title.bootstrap-widget div.bootstrap-widget-header h3.pull-right small', 'Finished');
        $this->checkSelectOptions('Finished', 'id=Election_status');
        
        //Check that mandate were created
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
    
    public function testVoteAdds()
    {
        $this->login("truvazia@gmail.com", "qwerty");
        
        $this->open('election/candidates/1');
        $this->waitForPageToLoad("30000");
        
        $voteBox = "css=div.checkbox.vote";
        $this->waitForElementPresent($voteBox);
        
        //check that all vote boxes are active
        $this->assertCssCount('css=div.checkbox.vote', 3);
        $this->assertCssCount('css=div.checkbox.vote.inactive', 0);        
        
        $this->click($voteBox);
        
        $this->waitForElementContainsText($voteBox . ' span.value', '✓');
        
        //check that other vote boxes are inactive
        $this->assertCssCount('css=div.checkbox.vote', 3);
        $this->assertCssCount('css=div.checkbox.vote.inactive', 2);
        
        $this->click('css=.user-info a.route');
        
        $this->waitForElementPresent('css=#votes-tab .items');
        $this->waitForCssCount('css=#votes-tab .items .user-info', 1);
        
        $this->assertLocation( TEST_BASE_URL . 'election/candidates/1/details/4');
        $this->waitForElementContainsText('css=.bootstrap-widget-header h3 .breadcrumbs', 'Elections/Election 1/Candidates/Another User');
        
        $this->waitForElementContainsText('css=#votes-tab .items .user-info .body > a', 'Vasiliy Pedak');
        $this->waitForElementContainsText('css=#candidate-info .body > a', 'Another User');
        $this->waitForElementContainsText('css=#candidate-info .body > b', '№1');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 1');
        
        $this->waitForElementPresent('css=#candidate-info .user-info .vote-cntr .checkbox.vote');
        $this->waitForElementContainsText('css=#candidate-info .user-info .vote-cntr .checkbox.vote span.value', '✓');
    }

    public function testVoteAddsFromCandidateDetails()
    {    
        $this->login("truvazia@gmail.com", "qwerty");
        
        $this->open('election/candidates/1/details/4');
        $this->waitForPageToLoad("30000");
        
        $voteBox = "css=#candidate-info div.checkbox.vote";
        $this->waitForElementPresent($voteBox);
        
        $this->assertCssCount('css=#votes-tab div.items .user-info', 0);
        
        $this->click($voteBox);
        
        $this->waitForElementContainsText($voteBox . ' span.value', '✓');
        
        $this->waitForCssCount('css=#votes-tab div.items .user-info', 1);
        $this->waitForElementContainsText('css=#votes-tab div.items .user-info a', 'Vasiliy Pedak');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 1'); 
    }
    
    public function testFilteringVotesFeedDoesNotAffectsAcceptedVotesCount()
    {
        $this->login("truvazia@gmail.com", "qwerty");
        
        $this->open('election/candidates/1/details/3');
        $this->waitForPageToLoad("30000");
        
        $this->waitForCssCount('css=#votes-tab div.items .user-info', 5);
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 3');
        $this->assertElementContainsText('css=#votes-tab a#items-count', 'Found 5');
        
        //check that loaded items correctly marked
        $this->waitForElementContainsText('css=#votes-tab .items .user-info:nth-of-type(4) .mark', 'Declined');
        $this->waitForElementContainsText('css=#votes-tab .items .user-info:nth-of-type(5) .mark', 'Revoked by elector');
        
        $this->type('css=#votes-tab input[name="userName"]', 'Another');
        $this->click('css=#votes-tab button.userName-filter-apply');
        
        $this->waitForCssCount('css=#votes-tab div.items .user-info', 2);
        $this->waitForElementContainsText('css=#votes-tab a#items-count', 'Found 2');
        $this->sleep(1000);
        $this->assertElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 3');
        
        $this->click('css=#votes-tab button.filter-reset');
        
        $this->waitForCssCount('css=#votes-tab div.items .user-info', 5);
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 3');
        $this->assertElementContainsText('css=#votes-tab a#items-count', 'Found 5');
        
        //Check that added vote correctly changes feed
        $voteBox = "css=#candidate-info div.checkbox.vote";
        $this->click($voteBox);
        $this->waitForElementContainsText($voteBox . ' span.value', '✓');
        
        $this->waitForCssCount('css=#votes-tab div.items .user-info', 6);
        $this->waitForElementContainsText('css=#votes-tab .items .user-info .body > a', 'Vasiliy Pedak');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 4');
        $this->waitForElementContainsText('css=#votes-tab a#items-count', 'Found 6');
        
        $this->click('css=#candidate-info .user-info .vote-cntr .checkbox.vote');
        
        $this->waitForElementContainsText('css=#votes-tab .items .user-info .body > a', 'Vasiliy Pedak');
        $this->waitForElementContainsText('css=#votes-tab .items .user-info .mark', 'Revoked by elector');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 3');
        $this->waitForCssCount('css=#votes-tab div.items .user-info', 6);
        $this->waitForElementContainsText('css=#votes-tab a#items-count', 'Found 6');
        
        //Check that declining vote correctly changes feed
        $this->click('css=#votes-tab div.user-info:nth-of-type(2) > .pull-right > .controls i.icon-remove-sign');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 2');
        $this->waitForCssCount('css=#votes-tab div.items .user-info', 6);
        $this->waitForElementContainsText('css=#votes-tab a#items-count', 'Found 6');
        $this->waitForElementContainsText('css=#votes-tab .items .user-info:nth-of-type(2) .mark', 'Revoked by elector');
    }
    
    public function testVoteRemovedByVoterDisplays()
    {
        $this->login("truvazia@gmail.com", "qwerty");
        
        $this->open('election/candidates/1');
        $this->waitForPageToLoad("30000");
        
        $voteBox = "css=div.checkbox.vote";
        $this->waitForElementPresent($voteBox);      
        //first vote
        $this->click($voteBox);
        
        $this->waitForElementContainsText($voteBox . ' span.value', '✓');
        
        $this->click('css=.user-info a.route');
        
        $this->waitForElementPresent('css=#votes-tab .items');
        $this->waitForCssCount('css=#votes-tab .items .user-info', 1);
        
        $this->assertElementContainsText('css=#votes-tab .items .user-info .body > a', 'Vasiliy Pedak');
        $this->assertElementContainsText('css=#candidate-info .body > a', 'Another User');
        $this->assertElementContainsText('css=#candidate-info .body > b', '№1');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 1');
        //revoke vote
        $this->click('css=#candidate-info .user-info .vote-cntr .checkbox.vote');
        
        $this->waitForElementContainsText('css=#votes-tab .items .user-info .body > a', 'Vasiliy Pedak');
        $this->waitForElementContainsText('css=#votes-tab .items .user-info .mark', 'Revoked by elector');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 0'); 
        $this->waitForCssCount('css=#votes-tab .items .user-info', 1);
        
        sleep(1);
        
        //vote again
        $this->click('css=#candidate-info .user-info .vote-cntr .checkbox.vote');
        
        $this->waitForCssCount('css=#votes-tab .items .user-info', 2);
        
        $this->assertElementContainsText('css=#votes-tab .items .user-info .body > a', 'Vasiliy Pedak');
        $this->assertElementContainsText('css=#candidate-info .body > a', 'Another User');
        $this->assertElementContainsText('css=#candidate-info .body > b', '№1');
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 1');
        
        //check that vote is marked after page refresh
        $this->open('election/candidates/1/details/4');
        $this->waitForPageToLoad("30000");
        $this->waitForElementContainsText($voteBox . ' span.value', '✓');
        
        //revoke again
        $this->click('css=#candidate-info .user-info .vote-cntr .checkbox.vote');
        
        $this->waitForElementContainsText('css=#candidate-info .body > div:nth-of-type(4)', 'Accepted votes count: 0');
        $this->sleep(750);
        $this->waitForCssCount('css=#votes-tab .items .user-info', 2);
        
        $this->waitForElementContainsText('css=#votes-tab .items .user-info:nth-of-type(1) .mark', 'Revoked by elector');
        $this->waitForElementContainsText('css=#votes-tab .items .user-info:nth-of-type(2) .mark', 'Revoked by elector');
    }
    
//    public function testUserWhichRemovedVoteCantVoteBecauseTimerLimits()
//    {
//        
//    }
 }
