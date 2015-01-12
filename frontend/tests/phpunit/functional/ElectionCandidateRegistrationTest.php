<?php
/**
 * Tests electors registration functionality
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionCandidateRegistrationTest extends WebTestCase
{
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'election' => array('Election', 'functional/electionRegistration/election'),
        'elector' => 'Elector',
        'vote' => 'Vote',
        'candidate' => array('Candidate', 'functional/electionRegistration/candidate'),
        'AuthAssignment' => array('AuthAssignment', 'functional/electionRegistration/AuthAssignment'),
        'election_auth_assignment' => array('ElectionAuthAssignment', 'functional/electionRegistration/election_auth_assignment')
    );

    public function testRegisterButtonDontShowsForUnauthorized()
    {
        $this->open('election/view/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-candidate');
        
        $this->open('election/provisions/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-candidate');
        
        $this->open('election/candidates/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-candidate');
        
        $this->open('election/electorate/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-candidate');      
    }
    
    public function testRegisterButtonShowsForAuthorizedNotRegistered()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        
        $this->open('election/view/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-candidate');
        
        $this->open('election/provisions/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-candidate');
        
        $this->open('election/candidates/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-candidate');
        
        $this->open('election/electorate/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-candidate');
    }
    
    public function testRegisterButtonNotShowsWhenElectionIsNotInRegistrationStatus()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        $election = Election::model()->findByPk(4);
        $statuses = array( 
            Election::STATUS_PUBLISHED, Election::STATUS_FINISHED, 
            Election::STATUS_CANCELED, Election::STATUS_ELECTION
        );
        
        foreach($statuses as $status) {
            $election->status = $status;
            $election->save(false);
            $this->assertRegisterBtn($present = false, 4);
        }
    }

    public function testRegisterButtonNotShowsWhenCandidateCanBeRegisteredOnlyByAdmin()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        $election = Election::model()->findByPk(4);
        $election->cand_reg_type = Election::CAND_REG_TYPE_ADMIN;
        $election->save(false);
        
        $this->assertRegisterBtn($present = false, 4);
    }
    
    public function testRegistrationButtonPressedByUserInElectionThatNotNeedConfirmation()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        $this->open('election/candidates/5');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-candidate');
        $this->sleep(300);
        $this->assertTextPresent('There is no items.');
        
        $this->click('css=#register-candidate');
        $this->waitForNotPresent('css=#register-candidate');
        
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(1) a', 'Another User');
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(1) .body > div:nth-of-type(3)', 'Registered');
        
        $this->assertCssCount('css=div.flash-messages div.alert', 1);
        $this->assertElementContainsText('css=div.flash-messages div.alert', 
            'You have been registered as candidate and elector'
        );
        $this->click('css=div.flash-messages div.alert a.close');
        $this->waitForNotPresent('css=div.flash-messages div.alert');
        
        $this->open('election/candidates/5');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-candidate');
        $this->sleep(800);
        $this->assertCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(1) a', 'Another User');
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(1) .body > div:nth-of-type(3)', 'Registered');
        
        $this->click('css=#all-cands-tab .items div.user-info:nth-of-type(1) a');
        $this->sleep(750);
        $this->assertCssCount('css=#candidate-details #controls button.confirm', 0);
    }   
    
    public function testRegistrationButtonPressedByUserInElectionThatNeedConfirmation()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        $this->open('election/candidates/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-candidate');
        $this->sleep(300);
        $this->assertTextPresent('There is no items.');
        
        $this->assertVisible('css=#all-cands-tab');
        $this->assertCssCount('css=#all-cands-tab .items div.user-info', 0);
        $this->assertElementContainsText('css=#all-cands-tab .items div', 
            'There is no items.'
        );
        
        $this->click('css=#register-candidate');
        $this->waitForNotPresent('css=#register-candidate');
        $this->sleep(500);
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementContainsText(
            'css=#all-cands-tab .items div.user-info a', 
            'Another User'
        );
        $this->assertElementContainsText(
            'css=#all-cands-tab .items div.user-info:nth-of-type(1) .body > div:nth-of-type(3)', 
            'Awaiting registration confirmation'
        );
        
        $this->assertCssCount('css=div.flash-messages div.alert', 1);
        $this->assertElementContainsText('css=div.flash-messages div.alert', 
            'Your registration request was sent. Election manager will consider it as soon as possible.'
        );
        $this->click('css=div.flash-messages div.alert a.close');
        $this->waitForNotPresent('css=div.flash-messages div.alert');        
        
        $this->open('election/candidates/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-candidate');
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info a', 'Another User');
        $this->assertElementContainsText(
            'css=#all-cands-tab .items div.user-info:nth-of-type(1) .body > div:nth-of-type(3)', 
            'Awaiting registration confirmation'
        );
        
        $this->logout();
        $this->waitForPageToLoad(5000);
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->waitForPageToLoad(5000);
        
        $this->open('election/candidates/4');
        $this->waitForPageToLoad(5000);
        
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info a', 'Another User');
        $this->assertElementContainsText(
            'css=#all-cands-tab .items div.user-info:nth-of-type(1) .body > div:nth-of-type(3)', 
            'Awaiting registration confirmation'
        );
        
        $this->click('css=#all-cands-tab .items div.user-info:nth-of-type(1) a');
        $this->waitForPresent('css=#candidate-details #controls button.confirm');
        
        $this->click('css=#candidate-details #controls button.confirm');
        $this->sleep(750);
        $this->waitForElementNotPresent('css=#candidate-details #controls button.confirm');
        $this->assertElementContainsText(
            'css=#candidate-info div.user-info:nth-of-type(1) .body > div:nth-of-type(3)', 
            'Registered'
        );
        $this->assertElementContainsText(
            'css=#candidate-info div.user-info:nth-of-type(1) .body > b:nth-of-type(1)', 
            '№1'
        );
        
        $this->click('css=ul.breadcrumbs > li:nth-of-type(3) > a.route');
        $this->sleep(350);
        $this->assertElementContainsText(
            'css=#all-cands-tab .items div.user-info:nth-of-type(1) .body > div:nth-of-type(3)', 
            'Registered'
        );
        
        $this->open('election/candidates/4');
        $this->waitForPageToLoad(5000);       
        $this->waitForCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementContainsText(
            'css=#all-cands-tab .items div.user-info:nth-of-type(1) .body > b:nth-of-type(1)', 
            '№1'
        );        
        $this->assertElementContainsText(
            'css=#all-cands-tab .items div.user-info:nth-of-type(1) .body > div:nth-of-type(3)', 
            'Registered'
        );
    }

    protected function assertRegisterBtn($present = true, $electionId, $pages = array('view','provisions','candidates','electorate'))
    {
        $sel = 'css=#register-candidate';
        foreach ($pages as $page) {
            $this->open('election/'.$page.'/'.$electionId);
            $this->waitForPageToLoad(5000);
            
            if($present)
                $this->assertElementPresent($sel, 'Register button should be present on the ' . $page . ' page');
            else
                $this->assertElementNotPresent($sel, 'Register button should not be present on the ' . $page . ' page');
        }
    }
}
