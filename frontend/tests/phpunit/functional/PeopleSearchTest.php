<?php
class PeopleSearchTest extends WebTestCase {
    
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile'
    );
    
    public function testShowAllUsers() {
        $this->openPeople();
        
        $this->assertTextPresent('Found 5 persones');
        
        $users = $this->getFixtureManager()->getRows('user_profile');
        
        unset($users[5]);   // remove inactive
        
        foreach ($users as $index => $user) {
            $this->assertElementPresent($selector = 'css=.list-view .items > .row-fluid:nth-child(' . ($index + 1) .')');
            $this->assertElementContainsText($selector . ' a', $user['first_name'] . ' ' . $user['last_name']);
            $this->assertElementContainsText($selector . ' .body', Yii::app()->dateFormatter->formatDateTime($user['birth_day'], 'medium', null));
            $this->assertElementContainsText($selector . ' .body', $user['birth_place']);
        }
    }
    
    public function testFilterByName() {
        $this->openPeople();
        
        $this->checkFindByName('Jhon Lenon');
        
        $this->checkFindByName('Pedak Vasiliy', array('Vasiliy Pedak'));
        
        $this->checkFindByName('Yetanother', array('Yetanother User'));
        
        $this->checkFindByName('use', array('Another User', 'Yetanother User'));
        
        $this->checkFindByName('foo', array());
    }
    
    public function testFilterByAge() {
        $this->openPeople();
        
        $this->type($selector = 'css=#PeopleSearch_ageFrom', 23);
        $this->click($submitSel = 'css=button[type="submit"]');
        $this->waitForPageToLoad();
        
        $this->assertCssCount($userSel = 'css=.list-view .items > .row-fluid', 1);
        $this->assertElementContainsText($userSel . ':nth-child(1)', 'Vasiliy Pedak');
    }
    
    public function testFilterByBirthDay() {
        $this->openPeople();
        
        $this->type($selector = 'css=#PeopleSearch_birth_day', '10/13/1993');
        $this->click($submitSel = 'css=button[type="submit"]');
        $this->waitForPageToLoad();
        
        $this->assertCssCount($userSel = 'css=.list-view .items > .row-fluid', 1);
        $this->assertElementContainsText($userSel . ':nth-child(1)', 'tester tester');    
    }
    
    public function testAgeFilterResets() {
        $this->openPeople();
        
        $this->type($selector = 'css=#PeopleSearch_birth_day', '10/13/1993');
        
        $this->focus($ageSel = 'css=#PeopleSearch_ageFrom');
        
        $this->type($ageSel, '23');
        $this->type($ageToSel = 'css=#PeopleSearch_ageTo', '25');
        
        $this->focus($selector);
        
        $this->assertValue($selector, '');
        
        $this->type($selector = 'css=#PeopleSearch_birth_day', '10/13/1993');
        $this->focus($ageSel);
        
        $this->assertValue($ageSel, '');
        $this->assertValue($ageToSel, '');
    }
    
    public function testFilterByBirthPlace() {
        $this->openPeople();
        
        $this->type($selector = 'css=#PeopleSearch_birth_place', 'tester');
        $this->click($submitSel = 'css=button[type="submit"]');
        $this->waitForPageToLoad();
        
        $namesFound = array('Another User', 'Jhon Lenon', 'Yetanother User', 'tester tester');
        
        $this->assertCssCount($userSel = 'css=.list-view .items > .row-fluid', count($namesFound));
        
        foreach ($namesFound as $index => $realName)
            $this->assertElementContainsText($userSel . ':nth-child(' . ($index + 1) . ')', $realName); 
    }
    
    public function testFilterReset() {
        $this->openPeople();
        
        $this->type($selector = 'css=#PeopleSearch_name', 'Vasiliy');
        $this->type($selector = 'css=#PeopleSearch_ageFrom', 23);
        $this->select("id=PeopleSearch_gender", "label=Male");
        $this->type($selector = 'css=#PeopleSearch_birth_place', 'Russia');
        
        $this->click($submitSel = 'css=button[type="submit"]');
        $this->waitForPageToLoad();
        
        $this->assertCssCount($userSel = 'css=.list-view .items > .row-fluid', 1);
        
        $this->assertElementContainsText($userSel . ':nth-child(1)', 'Vasiliy Pedak');
        
        $this->click('css=input[name="reset"]');
        $this->waitForPageToLoad();
        
        $this->assertCssCount($userSel = 'css=.list-view .items > .row-fluid', 5);
        
        foreach ($selectors = array('name', 'ageFrom', 'ageTo', 'birth_day', 'birth_place', 'gender') as $id)
            $this->assertValue('id=PeopleSearch_' . $id, '');
    }
    
    public function testFilterGender() {
        $this->openPeople();
        
        $this->select("id=PeopleSearch_gender", "label=Famale");
        $this->click($submitSel = 'css=button[type="submit"]');
        $this->waitForPageToLoad();
        
        $this->assertTextPresent('No results found');
        
        $this->assertCssCount($userSel = 'css=.list-view .items > .row-fluid', 0);
        
        $this->select("id=PeopleSearch_gender", "label=Male");
        $this->click($submitSel = 'css=button[type="submit"]');
        $this->waitForPageToLoad();
        
        $this->assertTextNotPresent('No results found');
        
        $this->assertCssCount($userSel = 'css=.list-view .items > .row-fluid', 5);
    }
    
    public function openPeople() {
        $this->open('people/index');
        $this->waitForElementPresent('css=div.summary');
    }
    
    protected function checkFindByName($name, $realNames = null) {
        
        if($realNames === null)
            $realNames = array($name);
        
        $this->type($selector = 'css=#PeopleSearch_name', $name);
        $this->click($submitSel = 'css=button[type="submit"]');
        $this->waitForPageToLoad();
        
        $this->assertCssCount($userSel = 'css=.list-view .items > .row-fluid', count($realNames));
        
        foreach ($realNames as $index => $realName)
            $this->assertElementContainsText($userSel . ':nth-child(' . ($index + 1) . ')', $realName);     
    }
}
