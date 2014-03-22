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
        'election_auth_assignment' => ':election_auth_assignment'
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
        
        $this->assertCssCount('css=#all-admins-tab .user-info', 3);
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
}
