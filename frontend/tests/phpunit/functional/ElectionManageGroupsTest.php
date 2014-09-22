<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionManageGroupsTest extends WebTestCase
{
    public $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'election' => 'Election',
        'AuthAssignment' => 'AuthAssignment',
        'election_auth_assignment' => ':election_auth_assignment'
    );

    public function testGroupsManagementMenuItemNotShowsForNotAdmins()
    {
        $election = Election::model()->findByPk(1);
        $election->status = Election::STATUS_REGISTRATION;
        $election->save();
        
        $this->open('election/view/1');
        $this->waitForPageToLoad();

        $anchorSel = 'link=Voters Groups';

        $this->assertElementNotPresent($anchorSel);
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('election/view/1');
        $this->waitForPageToLoad();
        
        $this->assertElementNotPresent($anchorSel);
        
        $this->logout();
        $this->login('vptester@mail.ru', 'qwerty');
        $this->open('election/view/1');
        $this->waitForPageToLoad();
        
        $this->assertElementPresent($anchorSel);
    }
    
    public function testNotAdminCantAccessGroupsManagementPage()
    {
        $this->open('election/manageVotersGroups/1');
        $this->waitForPageToLoad();
        $this->assertTextPresent('You have no rights to perform this action');
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('election/manageVotersGroups/1');
        $this->waitForPageToLoad();
        $this->assertTextPresent('You have no rights to perform this action');
        
        $this->logout();
        $this->login('vptester@mail.ru', 'qwerty');
        $this->open('election/manageVotersGroups/1');
        $this->waitForPageToLoad();
        $this->assertTextNotPresent('You have no rights to perform this action');
    }
}
