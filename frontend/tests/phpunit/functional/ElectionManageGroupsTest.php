<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionManageGroupsTest extends WebTestCase
{
    public $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'election' => array('Election', 'functional/electionManageGroupsTest/election'),
        'AuthAssignment' => 'AuthAssignment',
        'election_auth_assignment' => ':election_auth_assignment',
        'voter_group' => array('VoterGroup', 'functional/electionManageGroupsTest/voter_group'),
        'voter_group_member' => array('VoterGroupMember', 'functional/electionManageGroupsTest/voter_group_member'),
        'election_voter_group' => array('ElectionVoterGroup', 'functional/electionManageGroupsTest/election_voter_group'),
        'elector' => array('Elector', 'functional/electionManageGroupsTest/elector'),
        'elector_registration_request' => array('ElectorRegistrationRequest', 'functional/electionManageGroupsTest/elector_registration_request')
    );

    public function testGroupsManagementMenuItemNotShowsForNotAdmins()
    {
        $election = Election::model()->findByPk(1);
        $election->status = Election::STATUS_REGISTRATION;
        $election->save();
        
        $this->open('election/view/1');
        $this->waitForPageToLoad();

        $anchorSel = 'link=Voters and Groups Management';

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
    
    public function testGroupManagement()
    {
        $this->assertNull(
            VoterGroup::model()
                ->findByAttributes(array('election_id'=>3))
        );
        
        $this->openGroupsManagement(3);
        
        //Admin can create new local group

        $this->assertCssCount('css=#groups-grid table.x-grid-item', 1);
        
        $this->click('css=#groups-grid a#create-group-btn');
        
        $this->type('css=#groups-grid .x-grid-row-editor input[type="text"]', 'Group 1');
        $this->waitForElementPresent($updateBtnSel = 'css=#groups-grid .x-grid-row-editor .x-grid-row-editor-buttons a.x-btn');
        $this->waitForElementHasNoClass($updateBtnSel, 'x-item-disabled');
        $this->click($updateBtnSel);
        
        $this->waitForCssCount('css=#groups-grid table.x-grid-item', 2);
        $rowSel = 'css=#groups-grid table.x-grid-item:nth-of-type(2) ';
        $this->waitForElementNotPresent($rowSel . '.x-grid-dirty-cell');
        $this->assertEquals($newGroupId = 6, $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(2)'));
        $this->assertEquals('Group 1', $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(3)'));
        $this->assertEquals('Local', $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(4)'));
        $voterGroup = VoterGroup::model()->findByPk($newGroupId);
        $this->assertEquals('Group 1', $voterGroup->name);
        $this->assertEquals(VoterGroup::TYPE_LOCAL, $voterGroup->type);
        $this->assertEquals(3, $voterGroup->election_id);
        
        //edit
        
        $this->click($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(2)');
        $this->type('css=#groups-grid .x-grid-row-editor input[type="text"]', 'Group 1 Edited');
        $this->waitForElementPresent($updateBtnSel = 'css=#groups-grid .x-grid-row-editor .x-grid-row-editor-buttons a.x-btn');
        $this->waitForElementHasNoClass($updateBtnSel, 'x-item-disabled');
        $this->click($updateBtnSel);
        $this->waitForNotVisible('css=#groups-grid .x-grid-row-editor');
        $this->waitForElementNotPresent($rowSel . '.x-grid-dirty-cell');
        $this->assertEquals($newGroupId, $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(2)'));
        $this->assertEquals('Group 1 Edited', $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(3)'));
        $this->assertEquals('Local', $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(4)'));
        
        $voterGroup->refresh();
        $this->assertEquals('Group 1 Edited', $voterGroup->name);
        $this->assertEquals(VoterGroup::TYPE_LOCAL, $voterGroup->type);
        $this->assertEquals(3, $voterGroup->election_id);
        
        //add users
        $this->assertCount(0, $voterGroup->voterGroupMembers);
        
        $this->click($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(1)');
        $this->waitForElementPresent($tabHeaderSel = 'css=#members-tabs .x-tab-bar a.x-tab:nth-of-type(3)');
        $this->assertElementContainsText($tabHeaderSel, 'Group 1 Edited');
        $this->sleep(1500);
        $this->assertCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 0);
        
        $this->click('css=#members-tabs div[id|="groupmembersgrid"] a.x-btn');
        $this->waitForPresent('css=#add-electors-window div[id|="aes-operableusersgrid"]', 20000);
        $this->waitForCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item', 6, 20000);
        
        $this->click('css=#add-electors-window div[id|="aes-operableusersgrid"] .x-grid-header-ct .x-column-header-checkbox span');
        $this->assertCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item-selected', 6);
        
        $this->click('css=#add-electors-window-body #add-users-btn');
        $this->waitForCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item', 0, 10000);
        $this->click('css=#add-electors-window img.x-tool-close');
        
        $this->waitForCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 6);
        
        $this->sleep(1000);
        
        $this->assertCount(6, $voterGroup->getRelated('voterGroupMembers', true));
        
        //remove several users
        $this->click("css=#members-tabs div[id|=\"groupmembersgrid\"] table.x-grid-item:nth-of-type(1) "
                . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->controlKeyDown();
        $this->click("css=#members-tabs div[id|=\"groupmembersgrid\"] table.x-grid-item:nth-of-type(2) "
                . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->click("css=#members-tabs div[id|=\"groupmembersgrid\"] table.x-grid-item:nth-of-type(3) "
                . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->controlKeyUp();
        
        $this->assertCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item-selected', 3);
        
        $this->click('css=#members-tabs div[id|="groupmembersgrid"] a.x-btn:nth-of-type(2)');
        
        $this->confirm();
        
        $this->waitForCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 3);
        $this->assertCount(3, $voterGroup->getRelated('voterGroupMembers', true));
        
        //delete group
        
        $this->click($rowSel . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->click('id=remove-groups-btn');
        $this->confirm();
        
        $this->waitForCssCount('css=#groups-grid table.x-grid-item', 1);

        $this->sleep(1500);
        
        $this->assertNull(
            VoterGroup::model()
                ->findByAttributes(array('election_id'=>3))
        );
        $this->assertCount(0, 
            VoterGroupMember::model()
                ->findAllByAttributes(array('voter_group_id'=>$newGroupId))
        );
    }
    
    public function testGroupAssignmentAndUsersRegistration()
    {
        $this->openGroupsManagement(4);
        
        $rowSel = 'css=#groups-grid table.x-grid-item:nth-of-type({%index%}) ';
        
        $groupAssignCheck = $rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(5) div ';
        $groupViewBtn = $rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(1)';
        
        $this->waitForCssCount('css=#groups-grid table.x-grid-item', 2);
        
        $this->assertNull(
            ElectionVoterGroup::model()->findByAttributes(array(
                'election_id' => 4,
                'voter_group_id' => 1
            ))
        );
        
        //assign local
        $this->mouseDown($this->parseSel($groupAssignCheck, array('index'=>1)));
        $this->waitForElementPresent( 
            $this->parseSel($groupAssignCheck . '.x-grid-checkcolumn-checked', array('index'=>1))
        );
        
        $this->sleep(1500);
        
        $elVoterGroup = ElectionVoterGroup::model()->findByAttributes(array(
            'election_id' => 4,
            'voter_group_id' => 1
        ));
        $this->assertInstanceOf('ElectionVoterGroup', $elVoterGroup);
        
        //assign global
        $this->mouseDown($this->parseSel($groupAssignCheck, array('index'=>2)));
        $this->waitForElementPresent( 
            $this->parseSel($groupAssignCheck . '.x-grid-checkcolumn-checked', array('index'=>2))
        );
        
        $this->sleep(1500);
        
        $elVoterGroup = ElectionVoterGroup::model()->findByAttributes(array(
            'election_id' => 4,
            'voter_group_id' => 2
        ));
        $this->assertInstanceOf('ElectionVoterGroup', $elVoterGroup);
        
        //register
        $this->assertCssCount('css=#members-tabs #electorate table.x-grid-item', 0);
        
        $this->click('id=register-electors-btn');
        $this->closeModal();
        
        $this->waitForCssCount('css=#members-tabs #electorate table.x-grid-item', 3);
        $this->assertElementContainsText('css=#members-tabs #electorate', 'Vasiliy');
        $this->assertElementContainsText('css=#members-tabs #electorate', 'Another');
        $this->assertElementContainsText('css=#members-tabs #electorate', 'Jhon');
        
        $electors = Elector::model()->findAllByAttributes(
            array(
                'election_id' => 4
            )
        );
        
        $this->assertCount(3, $electors);
        
        //unasign local
        $this->mouseDown($this->parseSel($groupAssignCheck, array('index'=>1)));
        $this->waitForElementNotPresent( 
            $this->parseSel($groupAssignCheck . '.x-grid-checkcolumn-checked', array('index'=>2))   //was resorted
        );
        
        $this->sleep(1500);
        
        $elVoterGroup = ElectionVoterGroup::model()->findByAttributes(array(
            'election_id' => 4,
            'voter_group_id' => 1
        ));
        $this->assertNull($elVoterGroup);
        
        
        //unassign global
        $this->mouseDown($this->parseSel($groupAssignCheck, array('index'=>1)));
        $this->waitForElementNotPresent( 
            $this->parseSel($groupAssignCheck . '.x-grid-checkcolumn-checked', array('index'=>1))
        );
        
        $this->sleep(1500);
        
        $elVoterGroup = ElectionVoterGroup::model()->findByAttributes(array(
            'election_id' => 4,
            'voter_group_id' => 2
        ));
        $this->assertNull($elVoterGroup);
    }
    
    public function testGlobalGroupCantBeEditedOrRemoved()
    {
        $this->openGroupsManagement(4);
        $globalGroupSel = 'css=#groups-grid table.x-grid-item:nth-of-type(2) ';
        
        $groupViewBtn = $globalGroupSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(1)';
        $groupEditBtn = $globalGroupSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(2)';
        
        $this->waitForCssCount('css=#groups-grid table.x-grid-item', 2);
        
        //can't be edited
        $this->assertElementHasClass($groupEditBtn, 'x-item-disabled');
        $this->click($groupEditBtn);
        $this->sleep(500);
        $this->assertElementNotPresent('css=#groups-grid .x-grid-row-editor');
        
        //can't be removed
        $this->click($globalGroupSel . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->click('id=remove-groups-btn');
        $this->confirm();
        $this->sleep(750);
        $this->assertCssCount('css=#groups-grid table.x-grid-item', 2);
        
        //members list can't be changed
        $this->click($groupViewBtn);
        $this->waitForElementPresent('css=#members-tabs div[id|="groupmembersgrid"]');
        $this->waitForCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 2);
        $this->assertElementHasClass($addMembersBtn = 'css=#members-tabs div[id|="groupmembersgrid"] a.x-btn', 'x-btn-disabled');
        $this->assertElementHasClass($removeMembersBtn = 'css=#members-tabs div[id|="groupmembersgrid"] a.x-btn:nth-of-type(2)', 'x-btn-disabled');
        
        $this->click($addMembersBtn);
        $this->assertElementNotPresent('css=#add-electors-window');
        $this->click($removeMembersBtn);
        $this->assertNotVisible('css=.x-message-box');
    }
 
    public function testConfirmationAndDeclinationOfUsersRequests()
    {        
        $this->openGroupsManagement(5);
        $this->click('css=#members-tabs .x-tab-bar a.x-tab:nth-of-type(2)');
        
        $requestsPanel = 'css=#members-tabs div[id|="requestspanel"] ';
        $requestsGrid = $requestsPanel . 'div[id|="requestsgrid"] ';
        $gridItems = $requestsGrid . ' table.x-grid-item ';
        $detailsPanel = $requestsPanel . 'div[id|="requestdetail"].x-panel ';
        $detailsPanelMask = $detailsPanel . '.x-mask';
        $requestSel = $requestsPanel . 'table.x-grid-item:nth-of-type({%i%})';
        
        $this->waitForCssCount($gridItems, 6, 10000);
        
        // can accept from details panel without groups edit
        $this->assertElementContainsText($requestsGrid, 'Vasiliy');
        
        $this->click($this->parseSel($requestSel, array('i'=>1)));
        $this->waitForElementNotPresent($detailsPanelMask);
        
        $this->click($acceptBtn = $detailsPanel . '.x-toolbar a.x-btn:nth-of-type(2)');
        $this->waitForCssCount($gridItems, 5, 10000);
        $this->assertElementNotContainsText($requestsGrid, 'Vasiliy');
        
        // can accept from details panel with groups edit
        $this->click($this->parseSel($requestSel, array('i'=>1)));
        $this->waitForElementNotPresent($detailsPanelMask);
        
        //change group to 2
        $this->click($group1Check = $detailsPanel . 'div[id^="checkbox"].x-field:nth-of-type(1) input.x-form-checkbox');
        $this->sleep(350);
        $this->assertElementHasNoClass($detailsPanel . 'div[id^="checkbox"].x-field:nth-of-type(1)', 'x-form-cb-checked');
        $this->click($detailsPanel . 'div[id^="checkbox"].x-field:nth-of-type(2) input.x-form-checkbox');
        $this->sleep(350);
        $this->assertElementHasClass($detailsPanel . 'div[id^="checkbox"].x-field:nth-of-type(2)', 'x-form-cb-checked');
        
        $this->click($acceptBtn = $detailsPanel . '.x-toolbar a.x-btn:nth-of-type(2)');
        $this->waitForCssCount($gridItems, 4, 10000);
        $this->assertElementNotContainsText($requestsGrid, 'Another');
        
        // can accept many
        $this->click($this->parseSel($requestSel, array('i'=>1))
                . " td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->controlKeyDown();
        $this->click($this->parseSel($requestSel, array('i'=>2))
                . " td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->controlKeyUp();
        
        $this->click($requestsGrid . '.x-toolbar-docked-bottom a.x-btn');
        $this->waitForCssCount($gridItems, 2);
        
        // can decline many
        $this->click($this->parseSel($requestSel, array('i'=>1))
                . " td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->controlKeyDown();
        $this->click($this->parseSel($requestSel, array('i'=>2))
                . " td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->controlKeyUp();
        
        $this->click($requestsGrid . '.x-toolbar-docked-bottom a.x-btn:nth-of-type(2)');
        $this->waitForCssCount($gridItems, 0);
        
        $usersInGroup1 = array(1,3);
        $usersInGroup2 = array(1,2,3,4);
        
        $idsInGroup1 = Yii::app()->db->createCommand(
            'SELECT user_id FROM voter_group_member WHERE voter_group_id = 4 ORDER BY user_id'
        )->queryColumn();
        $idsInGroup2 = Yii::app()->db->createCommand(
            'SELECT user_id FROM voter_group_member WHERE voter_group_id = 5 ORDER BY user_id'
        )->queryColumn();
        
        $this->assertEquals($usersInGroup1, $idsInGroup1);
        $this->assertEquals($usersInGroup2, $idsInGroup2);
    }    
    
    public function testNewGlobalGroupManagement()
    {
        $this->openGlobalGroupsManagement();

        //Admin can create new global group

        $this->assertCssCount('css=#groups-grid table.x-grid-item', 4);
        
        $this->click('css=#groups-grid a#create-group-btn');
        
        $this->type('css=#groups-grid .x-grid-row-editor input[type="text"]', $grName = 'Global Group 2');
        $this->waitForElementPresent($updateBtnSel = 'css=#groups-grid .x-grid-row-editor .x-grid-row-editor-buttons a.x-btn');
        $this->waitForElementHasNoClass($updateBtnSel, 'x-item-disabled');
        $this->click($updateBtnSel);
        
        $this->waitForCssCount('css=#groups-grid table.x-grid-item', 5);
        $rowSel = 'css=#groups-grid table.x-grid-item:nth-of-type(5) ';
        $this->waitForElementNotPresent($rowSel . '.x-grid-dirty-cell');
        $this->assertEquals($newGroupId = 6, $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(2)'));
        $this->assertEquals($grName, $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(3)'));
        $this->assertEquals('Global', $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(4)'));
        $voterGroup = VoterGroup::model()->findByPk($newGroupId);
        $this->assertEquals($grName, $voterGroup->name);
        $this->assertEquals(VoterGroup::TYPE_GLOBAL, $voterGroup->type);
        $this->assertNull($voterGroup->election_id);
        
        //edit
        
        $this->click($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(2)');
        $this->type('css=#groups-grid .x-grid-row-editor input[type="text"]', $grName .= ' Edited');
        $this->waitForElementPresent($updateBtnSel = 'css=#groups-grid .x-grid-row-editor .x-grid-row-editor-buttons a.x-btn');
        $this->waitForElementHasNoClass($updateBtnSel, 'x-item-disabled');
        $this->click($updateBtnSel);
        $this->waitForNotVisible('css=#groups-grid .x-grid-row-editor');
        $this->waitForElementNotPresent($rowSel . '.x-grid-dirty-cell');
        $this->assertEquals($newGroupId, $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(2)'));
        $this->assertEquals($grName, $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(3)'));
        $this->assertEquals('Global', $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(4)'));
        
        $voterGroup->refresh();
        $this->assertEquals($grName, $voterGroup->name);
        $this->assertEquals(VoterGroup::TYPE_GLOBAL, $voterGroup->type);
        $this->assertNull($voterGroup->election_id);
        
        //add users
        $this->assertCount(0, $voterGroup->voterGroupMembers);
        
        $this->click($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(1)');
        $this->waitForElementPresent($tabHeaderSel = 'css=#members-tabs .x-tab-bar a.x-tab');
        $this->assertElementContainsText($tabHeaderSel, $grName);
        $this->sleep(1500);
        $this->assertCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 0);
        
        $this->click('css=#members-tabs div[id|="groupmembersgrid"] a.x-btn');
        $this->waitForPresent('css=#add-electors-window div[id|="aes-operableusersgrid"]', 20000);
        $this->waitForCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item', 6, 20000);
        
        $this->click('css=#add-electors-window div[id|="aes-operableusersgrid"] .x-grid-header-ct .x-column-header-checkbox span');
        $this->assertCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item-selected', 6);
        
        $this->click('css=#add-electors-window-body #add-users-btn');
        $this->waitForCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item', 0, 10000);
        $this->click('css=#add-electors-window img.x-tool-close');
        
        $this->waitForCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 6);
        
        $this->sleep(1000);
        
        $this->assertCount(6, $voterGroup->getRelated('voterGroupMembers', true));
        
        //remove several users
        $this->click("css=#members-tabs div[id|=\"groupmembersgrid\"] table.x-grid-item:nth-of-type(1) "
                . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->controlKeyDown();
        $this->click("css=#members-tabs div[id|=\"groupmembersgrid\"] table.x-grid-item:nth-of-type(2) "
                . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->click("css=#members-tabs div[id|=\"groupmembersgrid\"] table.x-grid-item:nth-of-type(3) "
                . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->controlKeyUp();
        
        $this->assertCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item-selected', 3);
        
        $this->click('css=#members-tabs div[id|="groupmembersgrid"] a.x-btn:nth-of-type(2)');
        
        $this->confirm();
        
        $this->waitForCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 3);
        $this->assertCount(3, $voterGroup->getRelated('voterGroupMembers', true));
        
        //delete group
        
        $this->click($rowSel . "td.x-grid-cell-row-checker div.x-grid-row-checker");
        $this->click('id=remove-groups-btn');
        $this->confirm();
        
        $this->waitForCssCount('css=#groups-grid table.x-grid-item', 4);
        $this->assertElementNotContainsText('css=#groups-grid', $grName);

        $this->sleep(1500);
        
        $this->assertNull(
            VoterGroup::model()
                ->findByPk($newGroupId)
        );
        $this->assertCount(0, 
            VoterGroupMember::model()
                ->findAllByAttributes(array('voter_group_id'=>$newGroupId))
        );
    }
    
    public function testCopiedGlobalGroupManagement()
    {
        $this->openGlobalGroupsManagement();

        $this->assertCssCount('css=#groups-grid table.x-grid-item', 4);
        $rowSel = 'css=#groups-grid table.x-grid-item:nth-of-type(5) ';
        // copy from local
        $this->click('css=#groups-grid table.x-grid-item '
            . 'td.x-grid-cell:nth-of-type(6) img:nth-of-type(3)');
        $this->waitForCssCount('css=#groups-grid table.x-grid-item', 5);
        $this->closeModal();
        
        $newGroupId = 6;
        $this->assertEquals($newGroupId, $this->getText( $rowSel
            . 'td.x-grid-cell:nth-of-type(2)'));
        $this->assertElementContainsText($rowSel
            . 'td.x-grid-cell:nth-of-type(3)', $grName = 'Local group 1');
        $this->assertElementContainsText($rowSel
            . 'td.x-grid-cell:nth-of-type(4)', 'Global');
        $this->assertEmpty($this->getText($rowSel
            . 'td.x-grid-cell:nth-of-type(5)'));
        //Global group can't be copied again
        $this->assertElementHasClass($rowSel
            . 'td.x-grid-cell:nth-of-type(6) img:nth-of-type(3)', 'x-item-disabled');
        
        $this->assertInstanceOf('VoterGroup', $voterGroup = $vg = VoterGroup::model()->findByAttributes(array(
            'name' => $grName,
            'type' => VoterGroup::TYPE_GLOBAL,
            'election_id' => null
        )));
        $this->assertEquals($newGroupId, $vg->id);
        
        // edit
        
        $this->click($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(2)');
        $this->type('css=#groups-grid .x-grid-row-editor input[type="text"]', $grName .= ' Edited');
        $this->waitForElementPresent($updateBtnSel = 'css=#groups-grid .x-grid-row-editor .x-grid-row-editor-buttons a.x-btn');
        $this->waitForElementHasNoClass($updateBtnSel, 'x-item-disabled');
        $this->click($updateBtnSel);
        $this->waitForNotVisible('css=#groups-grid .x-grid-row-editor');
        $this->waitForElementNotPresent($rowSel . '.x-grid-dirty-cell');
        $this->assertEquals($newGroupId, $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(2)'));
        $this->assertEquals($grName, $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(3)'));
        $this->assertEquals('Global', $this->getText($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(4)'));
        
        $voterGroup->refresh();
        $this->assertEquals($grName, $voterGroup->name);
        $this->assertEquals(VoterGroup::TYPE_GLOBAL, $voterGroup->type);
        $this->assertNull($voterGroup->election_id);
        
        //open members list
        $this->assertCount(2, $voterGroup->getRelated('voterGroupMembers', true));
        $this->click($rowSel . 'tr.x-grid-row td.x-grid-cell:nth-of-type(6) img:nth-of-type(1)');
        $this->waitForElementPresent($tabHeaderSel = 'css=#members-tabs .x-tab-bar a.x-tab');
        $this->assertElementContainsText($tabHeaderSel, $grName);
        $this->sleep(1500);
        $this->assertCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 2);
        $this->assertElementContainsText('css=#members-tabs div[id|="groupmembersgrid"]', 'Vasiliy');
        $this->assertElementContainsText('css=#members-tabs div[id|="groupmembersgrid"]', 'Another');
        
        
        // remove users
        $this->click('css=#members-tabs div[id|="groupmembersgrid"] .x-grid-header-ct .x-column-header-checkbox span');
        $this->assertCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item-selected', 2);
        
        $this->click('css=#members-tabs div[id|="groupmembersgrid"] a.x-btn:nth-of-type(2)');
        
        $this->confirm();
        
        $this->waitForCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 0);
        $this->assertCount(0, $voterGroup->getRelated('voterGroupMembers', true));
        
        // add users
        $this->click('css=#members-tabs div[id|="groupmembersgrid"] a.x-btn');
        $this->waitForPresent('css=#add-electors-window div[id|="aes-operableusersgrid"]', 20000);
        $this->waitForCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item', 6, 20000);
        
        $this->click('css=#add-electors-window div[id|="aes-operableusersgrid"] .x-grid-header-ct .x-column-header-checkbox span');
        $this->assertCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item-selected', 6);
        
        $this->click('css=#add-electors-window-body #add-users-btn');
        $this->waitForCssCount('css=#add-electors-window div[id|="aes-operableusersgrid"] table.x-grid-item', 0, 10000);
        $this->click('css=#add-electors-window img.x-tool-close');
        
        $this->waitForCssCount('css=#members-tabs div[id|="groupmembersgrid"] table.x-grid-item', 6);
        
        $this->sleep(1000);
        
        $this->assertCount(6, $voterGroup->getRelated('voterGroupMembers', true));
    }

    protected function openGroupsManagement($electionId)
    {
        $this->login('vptester@mail.ru', 'qwerty');
        $this->open('election/manageVotersGroups/' . $electionId);
        $this->waitForPageToLoad();
        $this->selectFrame('id=ElectoralGroups');
        $this->waitForElementPresent('id=groups-grid', 10000);
        $this->sleep(2500);
    }
    
    protected function openGlobalGroupsManagement()
    {
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->click("link=Vasiliy Pedak");
        $this->click("link=Manage Voter Groups");
        $this->waitForPageToLoad("30000");
        $this->selectFrame('id=ElectoralGroups');
        $this->waitForElementPresent('id=groups-grid', 10000);
        $this->sleep(2500);
    }
    
    protected function confirm()
    {
        //confirmation
        $this->waitForElementPresent('css=.x-message-box');     //dialog opened
        $this->waitForVisible('css=.x-message-box');
        $this->click('css=.x-message-box .x-toolbar a.x-btn:nth-of-type(2)');   //confirm
    }
    
    protected function closeModal()
    {
        $this->waitForElementPresent('css=.x-message-box');     //dialog opened
        $this->waitForVisible('css=.x-message-box');
        $this->click('css=.x-message-box .x-tool-close');
    }
}
