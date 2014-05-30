<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class MandateListingTest extends WebTestCase {
        
    public $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'target'       => array('Target', 'functional/mandateListing/target'),
        'election'     => array('Election', 'functional/mandateListing/election'),
        'mandate'      => array('Mandate', 'functional/mandateListing/mandate'),
        'candidate'    => array('Candidate', 'unit/electionProcess/candidate'),
//        'elector'      => array('Elector', 'unit/electionProcess/elector'),
    ); 
    
    public function testListsAndFiltersOnMandatesPage() {
        $this->open('');
        $this->waitForPageToLoad(3000);
        $this->click('link=Mandates');
        $this->waitForPageToLoad(3000);
        
        $this->assertMandatesPresent('0, superman');
        
        $this->select("name=status", "label=Active");
        $this->click("//input[@value='Filter']");

        $this->assertMandatesPresent('0');
        
        $this->click("//input[@value='Reset']");
        $this->assertMandatesPresent('0,superman');
        
        $this->type('name=name', 'Super');
        $this->click("//input[@value='Filter']");
        
        $this->assertMandatesPresent('superman');
        
        $this->click("//input[@value='Reset']");
        
        $this->type('name=owner_name', 'Pedak Vasiliy');
        usleep(250000);
        $this->click("//input[@value='Filter']");
        usleep(250000);
        $this->assertMandatesPresent('0');
    }
    
//    public function testListsOnUsersPage() {
//        
//    }    
    
    protected function assertMandatesPresent($aliases) {
        
        $aliases = AESHelper::explode($aliases);
        
        $mandatesContSel = 'css=#mandates-feed-container items';
        
        $this->waitForPresent('css=div.mandate');
        $this->waitForNotVisible('css=img.loader');
        
        usleep(300000);
        
        $count = count($aliases);
        
        $this->assertCssCount('css=div.mandate', $count);
        $this->waitForNotVisible('css=#mandates-feed-container .nav img.loader');
        $this->waitForElementContainsText('css=#mandates-feed-container .nav a#items-count', 'Found ' . $count, 4000, 500);
        
        foreach ($aliases as $alias)
            $this->assertMandatePresent($alias);        
    }

    protected function assertMandatePresent($fixtureAlias) {
        $this->assertTextPresent($this->getMandate($fixtureAlias)->name);
    }
    
    protected function assertMandateNotPresent($fixtureAlias) {
        $this->assertTextNotPresent($this->getMandate($fixtureAlias)->name);
    }
    
    protected function getMandate($fixtureAlias) {
        $mandate = $this->getFixtureManager()->getRecord('mandate', $fixtureAlias);
        
        if(!$mandate)
            throw new Exception ('Mandate not found by alias: ' . $fixtureAlias);
        
        return $mandate;
    }
    
}
