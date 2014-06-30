<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class UserVotesTest extends WebTestCase {
    
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'target'       => 'Target',
        'election'     => 'Election',
        'candidate'    => array('Candidate', 'functional/userVotes/candidate'),
        'AuthAssignment' => 'AuthAssignment',
        'election_auth_assignment' => ':election_auth_assignment',
        'vote' => array('Vote', 'functional/userVotes/vote')
    );
    
    public function testShowVotes() {
        $this->open('userPage/votes/3');
        usleep(50000);
        $this->waitForTextPresent('Election 2', 4000);
        usleep(50000);
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) h4', 'Election 1');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(1) a', '№2 Vasiliy Pedak');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(2) a', '№1 Another User');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(2) span.label', 'Declined');
        
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(2) h4', 'Election 2');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(2) .votes-container > div > div:nth-of-type(1) a', '№3 Jhon Lenon');
        $this->assertCssCount('css=#votes-feed-container > div > div.items > div:nth-of-type(2) .votes-container > div > div', 1);
        
        $this->type("name=name", "2");
        $this->click("css=.btn.form-submit");
        usleep(50000);
        $this->assertTextNotPresent('Election 1');
        $this->click("css=.btn.form-reset");
        $this->waitForTextPresent('Election 1');
        usleep(50000);
        $this->assertTextPresent('Election 2');
        
        $this->open('userPage/votes/2');
        usleep(50000);
        $this->waitForTextPresent('Election 1', 4000);
        $this->waitForTextPresent('Election 2', 4000);
        usleep(50000);
        
        $this->assertCssCount('css=#votes-feed-container > div > div.items > div', 2);
        $this->waitForCssCount('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div a', 4);
        
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) h4', 'Election 2');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(1) a', '№2 Vasiliy Pedak');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(2) a', '№3 Jhon Lenon');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(2) span.label', 'Declined');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(3) a', '№1 Another User');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(3) span.label', 'Revoked');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(4) a', '№1 Another User');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(1) .votes-container > div > div:nth-of-type(4) span.label', 'Revoked');
        
        $this->waitForCssCount('css=#votes-feed-container > div > div.items > div:nth-of-type(2) .votes-container > div > div a', 1);
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(2) h4', 'Election 1');
        $this->assertElementContainsText('css=#votes-feed-container > div > div.items > div:nth-of-type(2) .votes-container > div > div:nth-of-type(1) a', '№2 Vasiliy Pedak');    
    }
}
