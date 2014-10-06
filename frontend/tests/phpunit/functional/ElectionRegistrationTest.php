<?php
/**
 * Tests electors registration functionality
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionRegistrationTest extends WebTestCase
{
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'election' => array('Election', 'functional/electionRegistration/election'),
        'elector' => 'Elector',
        'elector_registration_request' => 'ElectorRegistrationRequest',
        'voter_group' => array('VoterGroup', 'functional/electionRegistration/voter_group'),
        'voter_group_member' => 'VoterGroupMember',
        'election_voter_group' => array('ElectionVoterGroup', 'functional/electionRegistration/election_voter_group'),
        'vote' => 'Vote',
        'candidate' => array('Candidate', 'functional/electionRegistration/candidate'),
        'AuthAssignment' => array('AuthAssignment', 'functional/electionRegistration/AuthAssignment'),
        'election_auth_assignment' => array('ElectionAuthAssignment', 'functional/electionRegistration/election_auth_assignment')
    );

    protected function setUp()
    {
        parent::setUp();

        $election = Election::model()->findByPk(1);
        $election->status = Election::STATUS_REGISTRATION;
        $election->save(false);
    }

    public function testNotAuthorizedCantRegister()
    {
        $this->open('election/electorate/1');
        $this->assertFalse($this->checkRegistrationAvailability());
    }

    public function testAuthorizedNotAdminCantRegister()
    {
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('election/electorate/1');
        $this->assertFalse($this->checkRegistrationAvailability());
    }

    public function testAdminCanRegister()
    {
        $this->loginAsAdmin();
        $this->open('election/electorate/1');
        $this->assertTrue($this->checkRegistrationAvailability());
    }

    public function testCanRegisterAndRemove()
    {
        $this->markTestIncomplete();
    }

    public function testAdminCantRegisterWithNotRegisterOrElectionStatus()
    {
        $this->loginAsAdmin();

        $statuses = array(
            Election::STATUS_PUBLISHED,
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
        
        $election->status = Election::STATUS_ELECTION;
        $election->save(false);

        $this->open('election/electorate/1');
        $this->assertTrue($this->checkRegistrationAvailability());
        
        $election->status = Election::STATUS_REGISTRATION;
        $election->save(false);

        $this->open('election/electorate/1');
        $this->assertTrue($this->checkRegistrationAvailability());
    }

    public function testRegisterButtonDoesnotShowsForUnauthorized()
    {
        $this->open('election/view/1');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-elector');
        
        $this->open('election/provisions/1');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-elector');
        
        $this->open('election/candidates/1');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-elector');
        
        $this->open('election/electorate/1');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-elector');      
    }
    
    public function testRegisterButtonShowsForAuthorizedNotRegistered()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        
        $this->open('election/view/1');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-elector');
        
        $this->open('election/provisions/1');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-elector');
        
        $this->open('election/candidates/1');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-elector');
        
        $this->open('election/electorate/1');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-elector');
    }

    public function testRegisterButtonShowsWhenElectionIsInElectionStatus()
    {        
        $this->login('tester1@mail.ru', 'qwerty');
        $this->assertRegisterBtn($present = true, 3);
    }    
    
    public function testRegisterButtonNotShowsWhenElectionIsNotInRegistrationOrElectionStatus()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        $this->assertRegisterBtn($present = false, 2);
        $this->assertRegisterBtn($present = false, 7);
        $this->assertRegisterBtn($present = false, 8);
    }

    public function testRegisterButtonNotShowsWhenElectorateCanBeRegisteredOnlyByAdmin()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        $this->assertRegisterBtn($present = false, 6);
    }
    
    public function testRegistrationButtonPressedByUserInElectionThatNotNeedConfirmation()
    {
        
        $this->login('tester1@mail.ru', 'qwerty');
        $this->open('election/electorate/5');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-elector');
        $this->sleep(300);
        $this->assertTextPresent('There is no items.');
        $this->assertNotVisible('css=#requested-tab-sel');
        
        $this->click('css=#register-elector');
        $this->waitForNotPresent('css=#register-elector');
        
        $this->waitForCssCount('css=#dest-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#dest-tab .items div.user-info:nth-of-type(1) a', 'Another User');
        
        $this->assertCssCount('css=div.flash-messages div.alert', 1);
        $this->assertElementContainsText('css=div.flash-messages div.alert', 
            'You have been registered as elector.'
        );
        $this->click('css=div.flash-messages div.alert a.close');
        $this->waitForNotPresent('css=div.flash-messages div.alert');
        
        $this->open('election/electorate/5');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-elector');
        $this->sleep('800');
        $this->assertCssCount('css=#dest-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#dest-tab .items div.user-info:nth-of-type(1) a', 'Another User');
    }   
    
    public function testRegistrationButtonPressedByUserInElectionThatNeedConfirmation()
    {
        
        $this->login('tester1@mail.ru', 'qwerty');
        $this->open('election/electorate/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-elector');
        $this->sleep(300);
        $this->assertTextPresent('There is no items.');
        
        $this->assertVisible('css=#requested-tab-sel');
        $this->assertCssCount('css=#requested-tab .items div.user-info', 0);
        $this->assertElementContainsText('css=#requested-tab .items div', 'There is no items.');
        
        $this->click('css=#register-elector');
        $this->waitForNotPresent('css=#register-elector');
        $this->sleep(300);
        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
        $this->assertCssCount('css=#requested-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
        
        $this->assertCssCount('css=div.flash-messages div.alert', 1);
        $this->assertElementContainsText('css=div.flash-messages div.alert', 
            'Your registration request was sent. Election manager will consider it as soon as possible.'
        );
        $this->click('css=div.flash-messages div.alert a.close');
        $this->waitForNotPresent('css=div.flash-messages div.alert');        
        
        $this->open('election/electorate/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-elector');
        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
        $this->waitForCssCount('css=#requested-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
        
        $this->logout();
        $this->waitForPageToLoad(5000);
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->waitForPageToLoad(5000);
        
        $this->open('election/electorate/4');
        $this->waitForPageToLoad(5000);
        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
        $this->waitForCssCount('css=#requested-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
        
//        @TODO: Rewrite code below to test registration acception from ExtJs
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
    }

    public function testRegistrationButtonPressedByUserInElectionThatNeedConfirmationAndElectionInElectionStatus()
    {   
        
        $this->login('tester1@mail.ru', 'qwerty');
        $this->open('election/electorate/9');
        $this->waitForPageToLoad(5000);
        $this->assertElementPresent('css=#register-elector');
        $this->sleep(300);
        $this->assertTextPresent('There is no items.');
        
        $this->assertVisible('css=#requested-tab-sel');
        $this->assertCssCount('css=#requested-tab .items div.user-info', 0);
        $this->assertElementContainsText('css=#requested-tab .items div', 'There is no items.');
        
        $this->click('css=#register-elector');
        $this->waitForNotPresent('css=#register-elector');
        $this->sleep(300);
        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
        $this->assertCssCount('css=#requested-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
        
        $this->assertCssCount('css=div.flash-messages div.alert', 1);
        $this->assertElementContainsText('css=div.flash-messages div.alert', 
            'Your registration request was sent. Election manager will consider it as soon as possible.'
        );
        $this->click('css=div.flash-messages div.alert a.close');
        $this->waitForNotPresent('css=div.flash-messages div.alert');        
        
        $this->open('election/electorate/9');
        $this->waitForPageToLoad(5000);
        $this->assertElementNotPresent('css=#register-elector');
        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
        $this->waitForCssCount('css=#requested-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
        
        $this->logout();
        $this->waitForPageToLoad(5000);
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->waitForPageToLoad(5000);
        
        $this->open('election/electorate/9');
        $this->waitForPageToLoad(5000);
        $this->assertElementContainsText('css=#dest-tab .items div', 'There is no items.');
        $this->waitForCssCount('css=#requested-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#requested-tab .items div.user-info a', 'Another User');
        
//        @TODO: Rewrite code below to test registration acception from ExtJs
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
    }    

    /**
     * User can vote right after "register as elector" button was clicked in 
     * active election which does not require elector registration confirmation
     */
    public function testElectorCanVoteInActiveElectionWithoutRegistrationConfirmationAfterRegisterBtnClicked()
    {
        $this->login('tester2@mail.ru', 'qwerty');
        $this->open('election/candidates/10');
        $this->waitForPageToLoad(5000);
        
        $this->waitForCssCount('css=#electoral-list-tab .items div.user-info', 3);
        $this->waitForCssCount('css=#electoral-list-tab .items div.checkbox.vote.inactive', 3);
        
        $this->click('css=#register-elector');
        $this->waitForNotPresent('css=#register-elector');
        
        $this->waitForCssCount('css=#electoral-list-tab .items div.checkbox.vote.inactive', 0);
        
        //Check that voting is available
        $voteBox = "css=div.checkbox.vote";
        
        //check that all vote boxes are active
        $this->assertCssCount('css=div.checkbox.vote', 3);
        $this->assertCssCount('css=div.checkbox.vote.inactive', 0);        
        
        $this->click($voteBox);
        
        $this->waitForElementContainsText($voteBox . ' span.value', '✓');
        
        //check that other vote boxes are inactive
        $this->assertCssCount('css=div.checkbox.vote', 3);
        $this->assertCssCount('css=div.checkbox.vote.inactive', 3);
    }

    /**
     * User can't vote right after "register as elector" button was clicked in 
     * active election which requires elector registration confirmation
     */
    public function testElectorCantVoteInActiveElectionWithRegistrationConfirmationAfterRegisterBtnClicked()
    {
        
        $this->login('tester2@mail.ru', 'qwerty');
        $this->open('election/candidates/9');
        $this->waitForPageToLoad(5000);

        $this->waitForCssCount('css=#electoral-list-tab .items div.user-info', 3);
        $this->waitForCssCount('css=#electoral-list-tab .items div.checkbox.vote.inactive', 3);
        
        $this->click('css=#register-elector');
        $this->waitForNotPresent('css=#register-elector');
        $this->sleep(500);
        
        $this->assertCssCount('css=div.flash-messages div.alert', 1);
        $this->assertElementContainsText('css=div.flash-messages div.alert', 
            'Your registration request was sent. Election manager will consider it as soon as possible.'
        );
        $this->click('css=div.flash-messages div.alert a.close');
        $this->waitForNotPresent('css=div.flash-messages div.alert');
        
        $this->assertCssCount('css=#electoral-list-tab .items div.checkbox.vote.inactive', 3);
    }    
    
    public function testRegisterInElectionWithAddingToGroupWithoutConfirmation()
    {
        $electionId = 11;
        
        $this->login('tester1@mail.ru', 'qwerty');
        $this->open('election/electorate/' . $electionId);
        $this->waitForPageToLoad();
        
        $this->waitForCssCount('css=#electoral-list-tab .items div.user-info', 0);
        
        $this->click('css=#register-elector');
        $this->sleep(500);
        $this->assertCssCount('css=div.flash-messages div.alert', 0);
        
        // Wait for visible modal
        $this->waitForPresent($modalSel = 'css=.modal');
        $this->waitForVisible($modalSel);
        
        // With visible local groups to select
        $election = Election::model()->findByPk($electionId);
        $availGroups = $election->localVoterGroups;
        $this->assertGreaterThan(0, $count = count($availGroups));
        $this->assertEquals(3, $count);
        
        $checkboxSel = 'css=div.modal-body > label.checkbox:nth-of-type({%index%}) > input';
        
        foreach ($availGroups as $index => $group) {
            $this->assertElementContainsText('css=.modal-body', $group->name);
            $this->assertEquals(
                $group->id, 
                $this->getAttribute(
                    $this->parseSel($checkboxSel, array('index' => $index+1)), 
                    'value'
                )
            );
        }
        //Check that register button is inactive
        $this->assertElementAttributeEquals(
            $regBtn = 'css=.modal-footer > button', 'disabled', 'disabled'
        );
        
        // Select several
        $this->click($this->parseSel($checkboxSel, array('index' => 1)));
        $this->click($this->parseSel($checkboxSel, array('index' => 2)));
        
        //Check that register button was activated
        $this->assertElementAttributeEquals(
            $regBtn, 'disabled', false
        );
        
        // Press submit button
        $this->click($regBtn);
        
        // Wait for modal hide
        $this->waitForNotPresent($modalSel);
        
        // Wait for #register-elector hide
        $this->waitForNotPresent('css=#register-elector');
        
        // Wait for notification present
        $this->assertCssCount('css=div.flash-messages div.alert', 1);
        $this->assertElementContainsText('css=div.flash-messages div.alert', 
            'You have been registered as elector.'
        );
        $this->click('css=div.flash-messages div.alert a.close');
        $this->waitForNotPresent('css=div.flash-messages div.alert');
        
        $this->waitForCssCount('css=#dest-tab .items div.user-info', 1);
        $this->assertElementContainsText('css=#dest-tab .items div.user-info:nth-of-type(1) a', 'Another User');
        
        // Look into DB and check that Elector really was added to selected groups
        $this->assertInstanceOf(VoterGroupMember, VoterGroupMember::model()->findByAttributes(array(
            'user_id' => 2,
            'voter_group_id' => $availGroups[0]->id
        )));
        $this->assertInstanceOf(VoterGroupMember, VoterGroupMember::model()->findByAttributes(array(
            'user_id' => 2,
            'voter_group_id' => $availGroups[1]->id
        )));
    }


    protected function checkRegistrationAvailability()
    {
        return ($this->isElementPresent('link=Voters and Groups Management') && $this->isVisible('link=Voters and Groups Management'));
    }

    protected function loginAsAdmin()
    {
        $this->login('vptester@mail.ru', 'qwerty');
    }

    protected function openTestElection($login = true)
    {
        if ($login)
            $this->loginAsAdmin();

        $this->open('election/electorate/1');
    }

    protected function assertRegisterBtn($present = true, $electionId, $pages = array('view','provisions','candidates','electorate'))
    {
        $sel = 'css=#register-elector';
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
