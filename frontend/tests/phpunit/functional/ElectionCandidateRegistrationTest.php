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
        
        $this->assertCssCount('css=div.flash-messages div.alert', 1);
        $this->assertElementContainsText('css=div.flash-messages div.alert', 
            'You have been registered as candidate.'
        );
        $this->click('css=div.flash-messages div.alert a.close');
        $this->waitForNotPresent('css=div.flash-messages div.alert');
        
        $this->open('election/candidates/5');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-candidate');
        $this->sleep(800);
        $this->assertCssCount('css=#all-cands-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#all-cands-tab .items div.user-info:nth-of-type(1) a', 'Another User');
    }   
    
//    public function testRegistrationButtonPressedByUserInElectionThatNeedConfirmation()
//    {
//        $this->login('tester1@mail.ru', 'qwerty');
//        $this->open('election/electorate/4');
//        $this->waitForPageToLoad(5000);
//        $this->assertElementPresent('css=#register-elector');
//        $this->sleep(300);
//        $this->assertTextPresent('There is no items.');
//        
//        $this->assertVisible('css=#requested-tab-sel');
//        $this->assertCssCount('css=#requested-tab .items div.user-info', 0);
//        $this->assertElementContainsText('css=#requested-tab .items div', 'There is no items.');
//        
//        $this->click('css=#register-elector');
//        $this->waitForNotPresent('css=#register-elector');
//        $this->sleep(300);
//        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
//        $this->assertCssCount('css=#requested-tab .items div.user-info', 1);
//        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
//        
//        $this->assertCssCount('css=div.flash-messages div.alert', 1);
//        $this->assertElementContainsText('css=div.flash-messages div.alert', 
//            'Your registration request was sent. Election manager will consider it as soon as possible.'
//        );
//        $this->click('css=div.flash-messages div.alert a.close');
//        $this->waitForNotPresent('css=div.flash-messages div.alert');        
//        
//        $this->open('election/electorate/4');
//        $this->waitForPageToLoad(5000);
//        $this->assertElementNotPresent('css=#register-elector');
//        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
//        $this->waitForCssCount('css=#requested-tab .items div.user-info', 1);
//        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
//        
//        $this->logout();
//        $this->waitForPageToLoad(5000);
//        
//        $this->login('truvazia@gmail.com', 'qwerty');
//        $this->waitForPageToLoad(5000);
//        
//        $this->open('election/electorate/4');
//        $this->waitForPageToLoad(5000);
//        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
//        $this->waitForCssCount('css=#requested-tab .items div.user-info', 1);
//        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
//        
//        $this->click('css=a[href="#requested-tab"]');
//        $this->mouseOver("css=#requested-tab .items div.user-info a");
//        $this->sleep(300);
//        $this->assertTrue($this->isVisible("css=#requested-tab .items div.user-info .controls .icon-ok"));
//        $this->click('css=#requested-tab .items div.user-info .controls .icon-ok');
//
//        $this->waitForElementNotPresent('css=#requested-tab .items div.user-info');
//
//        $this->click('css=a[href="#dest-tab"]');
//
//        $this->assertElementPresent('css=#dest-tab .items .user-info'); 
//        $this->assertElementContainsText('css=#dest-tab .items div.user-info a', 'Another User');
//        
//        $this->logout();
//        $this->open('election/electorate/4');
//        $this->waitForPageToLoad(5000);
//        $this->waitForPresent('css=#dest-tab .items .user-info');
//        $this->assertElementContainsText('css=#dest-tab .items div.user-info a', 'Another User');
//    }
//
//    public function testRegistrationButtonPressedByUserInElectionThatNeedConfirmationAndElectionInElectionStatus()
//    {
//        $this->login('tester1@mail.ru', 'qwerty');
//        $this->open('election/electorate/9');
//        $this->waitForPageToLoad(5000);
//        $this->assertElementPresent('css=#register-elector');
//        $this->sleep(300);
//        $this->assertTextPresent('There is no items.');
//        
//        $this->assertVisible('css=#requested-tab-sel');
//        $this->assertCssCount('css=#requested-tab .items div.user-info', 0);
//        $this->assertElementContainsText('css=#requested-tab .items div', 'There is no items.');
//        
//        $this->click('css=#register-elector');
//        $this->waitForNotPresent('css=#register-elector');
//        $this->sleep(300);
//        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
//        $this->assertCssCount('css=#requested-tab .items div.user-info', 1);
//        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
//        
//        $this->assertCssCount('css=div.flash-messages div.alert', 1);
//        $this->assertElementContainsText('css=div.flash-messages div.alert', 
//            'Your registration request was sent. Election manager will consider it as soon as possible.'
//        );
//        $this->click('css=div.flash-messages div.alert a.close');
//        $this->waitForNotPresent('css=div.flash-messages div.alert');        
//        
//        $this->open('election/electorate/9');
//        $this->waitForPageToLoad(5000);
//        $this->assertElementNotPresent('css=#register-elector');
//        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
//        $this->waitForCssCount('css=#requested-tab .items div.user-info', 1);
//        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
//        
//        $this->logout();
//        $this->waitForPageToLoad(5000);
//        
//        $this->login('truvazia@gmail.com', 'qwerty');
//        $this->waitForPageToLoad(5000);
//        
//        $this->open('election/electorate/9');
//        $this->waitForPageToLoad(5000);
//        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
//        $this->waitForCssCount('css=#requested-tab .items div.user-info', 1);
//        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
//        
//        $this->click('css=a[href="#requested-tab"]');
//        $this->mouseOver("css=#requested-tab .items div.user-info a");
//        $this->sleep(300);
//        $this->assertTrue($this->isVisible("css=#requested-tab .items div.user-info .controls .icon-ok"));
//        $this->click('css=#requested-tab .items div.user-info .controls .icon-ok');
//
//        $this->waitForElementNotPresent('css=#requested-tab .items div.user-info');
//
//        $this->click('css=a[href="#dest-tab"]');
//
//        $this->assertElementPresent('css=#dest-tab .items .user-info'); 
//        $this->assertElementContainsText('css=#dest-tab .items div.user-info a', 'Another User');
//        
//        $this->logout();
//        $this->open('election/electorate/9');
//        $this->waitForPageToLoad(5000);
//        $this->waitForPresent('css=#dest-tab .items .user-info');
//        $this->assertElementContainsText('css=#dest-tab .items div.user-info a', 'Another User');
//    }    
//
//    /**
//     * User can vote right after "register as elector" button was clicked in 
//     * active election which does not require elector registration confirmation
//     */
//    public function testElectorCanVoteInActiveElectionWithoutRegistrationConfirmationAfterRegisterBtnClicked()
//    {
//        $this->login('tester2@mail.ru', 'qwerty');
//        $this->open('election/candidates/10');
//        $this->waitForPageToLoad(5000);
//        
//        $this->waitForCssCount('css=#electoral-list-tab .items div.user-info', 3);
//        $this->waitForCssCount('css=#electoral-list-tab .items div.checkbox.vote.inactive', 3);
//        
//        $this->click('css=#register-elector');
//        $this->waitForNotPresent('css=#register-elector');
//        
//        $this->waitForCssCount('css=#electoral-list-tab .items div.checkbox.vote.inactive', 0);
//        
//        //Check that voting is available
//        $voteBox = "css=div.checkbox.vote";
//        
//        //check that all vote boxes are active
//        $this->assertCssCount('css=div.checkbox.vote', 3);
//        $this->assertCssCount('css=div.checkbox.vote.inactive', 0);        
//        
//        $this->click($voteBox);
//        
//        $this->waitForElementContainsText($voteBox . ' span.value', '✓');
//        
//        //check that other vote boxes are inactive
//        $this->assertCssCount('css=div.checkbox.vote', 3);
//        $this->assertCssCount('css=div.checkbox.vote.inactive', 3);
//    }
//
//    /**
//     * User can't vote right after "register as elector" button was clicked in 
//     * active election which requires elector registration confirmation
//     */
//    public function testElectorCantVoteInActiveElectionWithRegistrationConfirmationAfterRegisterBtnClicked()
//    {
//        $this->login('tester2@mail.ru', 'qwerty');
//        $this->open('election/candidates/9');
//        $this->waitForPageToLoad(5000);
//
//        $this->waitForCssCount('css=#electoral-list-tab .items div.user-info', 3);
//        $this->waitForCssCount('css=#electoral-list-tab .items div.checkbox.vote.inactive', 3);
//        
//        $this->click('css=#register-elector');
//        $this->waitForNotPresent('css=#register-elector');
//        $this->sleep(500);
//        
//        $this->assertCssCount('css=div.flash-messages div.alert', 1);
//        $this->assertElementContainsText('css=div.flash-messages div.alert', 
//            'Your registration request was sent. Election manager will consider it as soon as possible.'
//        );
//        $this->click('css=div.flash-messages div.alert a.close');
//        $this->waitForNotPresent('css=div.flash-messages div.alert');
//        
//        $this->assertCssCount('css=#electoral-list-tab .items div.checkbox.vote.inactive', 3);
//    }    
//    
//    protected function checkRegistrationAvailability()
//    {
//        return ($this->isVisible('css=#source-tab-sel') && $this->isElementPresent('css=#source-tab div.items'));
//    }

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
