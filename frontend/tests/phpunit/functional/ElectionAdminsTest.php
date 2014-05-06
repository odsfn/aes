<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionAdminsTest extends WebTestCase {

    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'election'     => 'Election',
        'AuthAssignment' => 'AuthAssignment',
        'election_auth_assignment' => ':election_auth_assignment',
        'election_comment' => array('ElectionComment', 'functional/electionAdmins/election_comment')
    );
    
    public function testAdminsManagementShowsForUnauthorized() {
        $this->open('election/view/1');
        $this->waitForPageToLoad();
        
        $adminsAnchorSel = 'link=Admins';
        
        $this->assertElementPresent($adminsAnchorSel);
        $this->click($adminsAnchorSel);
        $this->waitForPageToLoad();
        
        $this->assertElementPresent($adminsListTabsel = 'css=a[href="#all-admins-tab"]');
        $this->assertVisible($adminsListTabsel);
        $this->assertNotVisible('css=a[href="#invite-tab"]');
        
        $this->waitForCssCount('css=#all-admins-tab .user-info', 3);
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Jhon Lenon');
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Another User');
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Yetanother User');
        
        //can`t manage check here
        $this->mouseOver('css=.user-info:nth-of-type(2)');
        $this->assertNotVisible('css=.user-info:nth-of-type(2) .controls small');
    }
    
    public function testAuthorizedCreatorCanManage() {
        $this->login('vptester@mail.ru', 'qwerty');
        $this->open('election/admins/1');
        $this->waitForPageToLoad();
        
        $this->waitForElementPresent('css=#all-admins-tab .user-info');
        $this->mouseOver('css=.user-info');
        $this->assertNotVisible('css=.user-info .controls small');
        
        $secondAdminSel = 'css=.user-info:nth-of-type(2) '; 
        $this->mouseOver($secondAdminSel);
        $this->assertVisible($depriveSecondSel = $secondAdminSel . '.controls small');
        $this->click($depriveSecondSel);
        $this->waitForCssCount('css=#all-admins-tab .user-info', 2);
        $this->assertElementNotContainsText('css=#all-admins-tab .items', 'Another User');
        
        $this->click('css=a[href="#invite-tab"]');
        $this->mouseOver('css=#invite-tab .user-info');
        $this->assertVisible('css=#invite-tab .user-info .controls small');
        $this->click('css=#invite-tab .user-info .controls small');
        $this->waitForText('css=#invite-tab .user-info:nth-of-type(1) .mark small', 'Empovered');
        
        $this->click('css=a[href="#all-admins-tab"]');
        $this->assertCssCount('css=#all-admins-tab .user-info', 3);
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Vasiliy Pedak');
    }
    
    public function testAuthorizedNotCreatorCantManage() {
        $this->login('tester1@mail.ru', 'qwerty');
        $this->open('election/admins/1');
        $this->waitForPageToLoad();
        $this->waitForCssCount('css=#all-admins-tab .user-info', 3);
        
        $this->assertNotVisible('css=a[href="#invite-tab"]');
        $this->mouseOver('css=#all-admins-tab .user-info:nth-of-type(3)');
        $this->assertNotVisible('css=#all-admins-tab .user-info:nth-of-type(3) .controls small');
    }
    
    public function testEveryCanFilter() {
        $this->open('election/admins/1');
        
        $this->waitForCssCount('css=#all-admins-tab .user-info', 3);
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Jhon Lenon');
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Another User');
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Yetanother User');
        
        $this->type('css=#all-admins-tab input[name="userName"]', 'Jho');
        $this->click('css=#all-admins-tab .userName-filter-apply');
        $this->waitForCssCount('css=#all-admins-tab .user-info', 1);
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Jhon Lenon');
        
        $this->click('css=#all-admins-tab .filter-reset');
        $this->waitForCssCount('css=#all-admins-tab .user-info', 3);
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Jhon Lenon');
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Another User');
        $this->assertElementContainsText('css=#all-admins-tab .items', 'Yetanother User');
    }
    
    public function testProvisionsShowsForUnauthorized() {
        $this->checkProvisionsShowsForNotAdmin(0);
        
        $newPostBoxSel = 'css=input[name="new-post"]';
        $this->assertElementPresent($newPostBoxSel);
        $this->assertNotVisible($newPostBoxSel);
    }
    
    public function testProvisionsShowsForAuthorizedNotAdmin() {
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->checkProvisionsShowsForNotAdmin(0);
        
        $newPostBoxSel = 'css=input[name="new-post"]';
        $this->assertElementPresent($newPostBoxSel);
        $this->assertVisible($newPostBoxSel);
    }
    
    public function testProvisionsShowsForAdmin() {
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->checkProvsionsShows(1, true);
        
        $newPostBoxSel = 'css=input[name="new-post"]';
        $this->assertElementPresent($newPostBoxSel);
        $this->assertVisible($newPostBoxSel);
        
        $this->waitForElementPresent('css=.comments-feed .post .post-body', 3000);
        
        //Check that admin can edit not own comments
        $this->assertElementContainsText('css=.comments-feed .post span.user', 'Jhon Lenon');
        $this->mouseOver('css=.comments-feed .post .post-body');
        $this->assertVisible('css=.comments-feed .post .post-body .icon-remove');
    }
    
    public function testAuthorizedNotAdminCantEditNotOwnComments() {
        $this->login('tester3@mail.ru', 'qwerty');
        $election = $this->getFixtureManager()->getRecord('election', 1);
        $this->open('election/provisions/' . $election->id);
        
        $this->waitForElementPresent('css=.comments-feed .post .post-body', 3000);
        
        //Check that admin can not edit not own comments
        $this->assertElementContainsText('css=.comments-feed .post span.user', 'Jhon Lenon');
        $this->mouseOver('css=.comments-feed .post .post-body');
        $this->assertElementPresent('css=.comments-feed .post .post-body .icon-remove');
        $this->assertNotVisible('css=.comments-feed .post .post-body .icon-remove');
    }
    
    public function testProvisionsEditByAdmin() {
        $this->login('truvazia@gmail.com', 'qwerty');
        
        $election = $this->getFixtureManager()->getRecord('election', 1);
                
        $this->open('election/provisions/' . $election->id);
        $this->click('css=#election-info h5 a');
        $this->waitForPageToLoad(3000);
        
        $newMandate = $election->mandate . ' edited';
        $mandateFieldSel = 'css=input[name="Election[mandate]"]';
        
        $this->type($mandateFieldSel, $newMandate);
        $this->click('css=#ElectionForm .form-actions button.btn-primary');
        $this->waitForPageToLoad(3000);
        
        $this->assertElementPresent('css=.flash-messages .alert-success');
        $this->assertVisible('css=.flash-messages .alert-success');
        
        $this->assertValue($mandateFieldSel, $newMandate);
    }
    
    protected function checkProvisionsShowsForNotAdmin($electionIndex) 
    {
        $this->checkProvsionsShows($electionIndex);
    }
    
    protected function checkProvsionsShows($electionIndex, $editable = false) {
        $election = $this->getFixtureManager()->getRecord('election', $electionIndex);
        
        $this->open('election/view/' . $election->id);
        $this->waitForPageToLoad(3000);
        
        $this->assertElementPresent('link=Provisions');
        
        $this->click("link=Provisions");
        $this->waitForPageToLoad("3000");

        $this->assertTextPresent('Mandate');
        $this->assertTextPresent('Candidate registraion options');
        $this->assertTextPresent('Electorate registraion options');
        
        if(!$editable)
            $this->assertElementNotPresent('css=#election-info h5 a');
        else
            $this->assertElementPresent('css=#election-info h5 a');
        
        $this->assertElementContainsText('css=table tbody tr td', $election->mandate);        
    }
}
