<?php
class PetitionsOnMandateDetailsTest extends WebTestCase
{
    protected $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'target'       => array('Target', 'functional/mandateListing/target'),
        'election'     => array('Election', 'functional/mandateListing/election'),
        'mandate'      => array('Mandate', 'functional/mandateListing/mandate'),
        'candidate'    => array('Candidate', 'unit/electionProcess/candidate'),
        'petition'     => array('Petition', 'functional/petitionsOnMandateDetails/petition')
    );
    
    public function testPetitionsLists()
    {
        $this->open('mandate/index/details/1/');
        $this->waitForPresent($this->getCssSel('container'));
        
        $this->click("link=Petitions");

        $this->assertPetitionsFeedPresent();
        $this->assertPetitionsTotalCountIs($count = 15);
        $this->assertPetitionsOnPageCountIs($onPageCount = $count);
        
        $petitions = Petition::model()->findAllByAttributes(array('mandate_id'=>1));
        
        $this->assertPetitionsFeedContains($petitions);
    }
    
    public function testPetitionsFilters()
    {
        $this->open('mandate/index/details/1/');
        $this->waitForPresent($this->getCssSel('container'));
        
        $this->click("link=Petitions");
        $this->assertPetitionsFeedPresent();
        
        $this->type("css=input[name='title']", "еще");
        $this->click($this->getCssSel('petitionsFeed') . ' input.form-submit');
        
        $this->assertPetitionsTotalCountIs($count = 2);
        $this->assertPetitionsOnPageCountIs($count);
        
        $this->type("css=input[name='creator_name']", "NOBODY");
        $this->click($this->getCssSel('petitionsFeed') . ' input.form-submit');
        
        $this->assertPetitionsTotalCountIs($count = 0);
        $this->assertElementContainsText($this->getCssSel('petitionsFeed.item'), 'There is no items.');
        
        $this->type("css=input[name='creator_name']", "vasiliy");
        $this->click($this->getCssSel('petitionsFeed') . ' input.form-submit');
        
        $this->assertPetitionsTotalCountIs($count = 2);
        $this->assertPetitionsOnPageCountIs($count);
    }

    public function testPetitionDetailsShows()
    {
        $petitions = Petition::model()->findAllByAttributes(array('mandate_id'=>1), array('order'=>'created_ts DESC'));
        
        $this->open('mandate/index/details/1/');
        $this->waitForPresent($this->getCssSel('container'));
        
        $this->click("link=Petitions");
        $this->assertPetitionsFeedPresent();

        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petitions');
        $this->assertCssCount($this->getCssSel('tabs.tab'), 2);
        
        $this->click($this->getCssSel('petitionsFeed.item') . ' h4 > a');
        
        $tabTitle = $this->getText($this->getCssSel('petitionsFeed.item') . ' h4 > a');
        $this->waitForCssCount($this->getCssSel('tabs.tab'), 3);
        $openedPetition = $petitions[0];
        
        $this->assertEquals($openedPetition->title, $tabTitle);
        $this->assertEquals($openedPetition->title, $this->getText($this->getCssSel('tabs.tab') . ':nth-of-type(3)'));
        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petition_' . $openedPetition->id);
        
        $this->click($this->getCssSel('petitionsFeed.item') . ' h4 > a');
        $this->waitForCssCount($this->getCssSel('tabs.tab'), 3);
        
        $this->click($this->getCssSel('petitionsFeed.item') . ':nth-of-type(2) h4 > a');
        $this->waitForCssCount($this->getCssSel('tabs.tab'), 4);
        $openedPetition = $petitions[1];
        $this->assertEquals($openedPetition->title, $this->getText($this->getCssSel('tabs.tab') . ':nth-of-type(4)'));
        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petition_' . $openedPetition->id);
        
        $this->click($this->getCssSel('petitionsFeed.item') . ':nth-of-type(3) h4 > a');
        $this->waitForCssCount($this->getCssSel('tabs.tab'), 5);
        $openedPetition = $petitions[2];
        $this->assertEquals($openedPetition->title, $this->getText($this->getCssSel('tabs.tab') . ':nth-of-type(5)'));
        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petition_' . $openedPetition->id);

        //выбираем открытую петицию - видем ее локейшн
        $this->click($this->getCssSel('tabs.tab') . ':nth-of-type(4) > a');
        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petition_' . $petitions[1]->id);
        
        //закрываем открытую петицию - видим локейшн предидущей
        $this->click($this->getCssSel('tabs.tab') . ':nth-of-type(4) > a > span.icon-remove');
        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petition_' . $petitions[0]->id);
        
        //тыкаем по уже открытой, из списка петиций - получаем ее локейшн
        $this->click($this->getCssSel('petitionsFeed.item') . ' h4 > a');
        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petition_' . $petitions[0]->id);
        
    }
//    testMultiplePetitionsDetailsShowsAndCloses    
//    testPetitionOpens
//    testPetitionCreates
//    testPetitionCanBeSupported
    protected function getCssSelectors()
    {
        return array(
            'container' => '#mandate-details > div',
            'tabs'      => '#mandate-tabs > div.tabs-container > ul.nav-tabs',
            'tabs.tab'  => ' > li',
            'petitionsFeed' => '#petitions-feed-container > div',
            'petitionsFeed.count' => ' a#items-count',
            'petitionsFeed.item'  => ' > .items > div',
            'petitionsFeed.item.title' => ' h4 > a'
        );
    }
    
    protected function assertPetitionsFeedPresent()
    {
        $this->waitForPresent($this->getCssSel('petitionsFeed'));
    }
    
    protected function assertPetitionsTotalCountIs($count)
    {
        $this->waitForElementContainsText($this->getCssSel('petitionsFeed.count'), 'Found ' . $count);
    }
    
    protected function assertPetitionsOnPageCountIs($count)
    {
        $that = $this;
        $sel = $that->getCssSel('petitionsFeed.item');
        $this->waitFor(function() use(&$that, $count, $sel) {
            return ($count == $that->getCssCount($sel));
        });
    }
    
    protected function getFeedItemsCount($sel)
    {
        return $this->getCssCount($this->getCssSel($sel));
    }
    
    protected function assertPetitionsFeedContains($petitions)
    {
        $notFound = array();
        $feedItemsCount = $this->getFeedItemsCount('petitionsFeed.item');
        
        for ($index = 1; $index <= $feedItemsCount; $index++) {
            
            $text = $this->getText($this->getCssSel('petitionsFeed.item') . ':nth-of-type(' . $index . ') h4 > a');
            
            foreach ($petitions as $i => $petition) {
                if ($petition->title === $text) {
                    unset($petitions[$i]);
                    $notFoundPetition = false;
                    break;
                } else {
                    $notFoundPetition = true;
                }
            }
            
            if ($notFoundPetition) {
                $notFound[] = 'ID: ' . $petition->id . ' Title: ' . $petition->title;
            }
        }
        
        $this->assertEquals($feedItemsCount, $index-1);
        $this->assertEquals(0, count($notFound), 'Not found petitions: ' . print_r($notFound, true));
    }
}

