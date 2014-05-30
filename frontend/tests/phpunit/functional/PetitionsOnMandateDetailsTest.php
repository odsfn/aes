<?php
class PetitionsOnMandateDetailsTest extends WebTestCase
{
    protected $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'target'       => array('Target', 'functional/mandateListing/target'),
        'election'     => array('Election', 'functional/mandateListing/election'),
        'mandate'      => array('Mandate', 'functional/petitionsOnMandateDetails/mandate'),
        'candidate'    => array('Candidate', 'unit/electionProcess/candidate'),
        'petition'     => array('Petition', 'functional/petitionsOnMandateDetails/petition'),
        'petition_rate'=> array('PetitionRate', 'functional/petitionsOnMandateDetails/petition_rate'),
        'vote'         => array('Vote', 'functional/petitionsOnMandateDetails/vote')
    );

    public function testPetitionsLists()
    {
        $this->open('mandate/index/details/1/');
        $this->waitForPresent($this->getCssSel('container'));
        usleep(50000);
        $this->click("link=Petitions");
        usleep(50000);
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
        usleep(50000);
        $this->click("link=Petitions");
        usleep(50000);        
        $this->assertPetitionsFeedPresent();

        $this->type("css=input[name='title']", "еще");
        $this->click($this->getCssSel('petitionsFeed') . ' input.form-submit');
        usleep(50000);
        $this->assertPetitionsTotalCountIs($count = 2);
        $this->assertPetitionsOnPageCountIs($count);

        $this->type("css=input[name='creator_name']", "NOBODY");
        $this->click($this->getCssSel('petitionsFeed') . ' input.form-submit');
        usleep(50000);
        $this->assertPetitionsTotalCountIs($count = 0);
        $this->assertElementContainsText($this->getCssSel('petitionsFeed.item'), 'There is no items.');

        $this->type("css=input[name='creator_name']", "vasiliy");
        $this->click($this->getCssSel('petitionsFeed') . ' input.form-submit');
        usleep(50000);
        $this->assertPetitionsTotalCountIs($count = 2);
        $this->assertPetitionsOnPageCountIs($count);
    }

    public function testPetitionDetailsShows()
    {
        $petitions = Petition::model()->findAllByAttributes(array('mandate_id'=>1), array('order'=>'created_ts DESC'));

        $this->open('mandate/index/details/1/');
        $this->waitForPresent($this->getCssSel('container'));
        usleep(50000);
        $this->click("link=Petitions");
        usleep(50000);
        $this->assertPetitionsFeedPresent();

        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petitions');
        $this->assertCssCount($this->getCssSel('tabs.tab'), 2);

        $this->click($this->getCssSel('petitionsFeed.item') . ' h4 > a');
        usleep(50000);
        $tabTitle = $this->getText($this->getCssSel('petitionsFeed.item') . ' h4 > a');
        $this->waitForCssCount($this->getCssSel('tabs.tab'), 3);
        $openedPetition = $petitions[0];
        usleep(50000);
        $this->assertEquals($openedPetition->title, $tabTitle);
        $this->assertEquals($openedPetition->title, $this->getText($this->getCssSel('tabs.tab') . ':nth-of-type(3)'));
        $this->assertActivePetition($openedPetition);

        $this->click($this->getCssSel('petitionsFeed.item') . ' h4 > a');
        $this->waitForCssCount($this->getCssSel('tabs.tab'), 3);
        usleep(50000);
        $this->click($this->getCssSel('petitionsFeed.item') . ':nth-of-type(2) h4 > a');
        $this->waitForCssCount($this->getCssSel('tabs.tab'), 4);
        usleep(50000);        
        $openedPetition = $petitions[1];
        $this->assertEquals($openedPetition->title, $this->getText($this->getCssSel('tabs.tab') . ':nth-of-type(4)'));
        $this->assertActivePetition($openedPetition);

        $this->click($this->getCssSel('petitionsFeed.item') . ':nth-of-type(3) h4 > a');
        $this->waitForCssCount($this->getCssSel('tabs.tab'), 5);
        usleep(50000);        
        $openedPetition = $petitions[2];
        $this->assertEquals($openedPetition->title, $this->getText($this->getCssSel('tabs.tab') . ':nth-of-type(5)'));
        $this->assertActivePetition($openedPetition);

        //выбираем открытую петицию - видем ее локейшн
        $this->click($this->getCssSel('tabs.tab') . ':nth-of-type(4) > a');
        usleep(50000);
        $this->assertActivePetition($petitions[1]);

        //закрываем открытую петицию - видим локейшн предидущей
        $this->click($this->getCssSel('tabs.tab') . ':nth-of-type(4) > a > span.icon-remove');
        usleep(50000);
        $this->assertActivePetition($petitions[0]);

        //тыкаем по уже открытой, из списка петиций - получаем ее локейшн
        $this->click($this->getCssSel('petitionsFeed.item') . ' h4 > a');
        usleep(50000);
        $this->assertActivePetition($petitions[0]);
    }

    public function testPetitionOpens()
    {
        $petitionId = 11;
        $petition = Petition::model()->findByPk($petitionId);

        $this->open('mandate/index/details/1/petition_' . $petitionId);
        $this->waitForPresent($this->getCssSel('container'));
        usleep(50000);
        $this->assertPetitionsFeedPresent();

        $this->assertActivePetition($petition);
    }

    public function testPetitionCantBeCreatedByUnauthorized()
    {
        $this->open('mandate/index/details/1/');
        $this->waitForPresent($this->getCssSel('container'));
        usleep(50000);
        $this->assertElementNotPresent($this->getCssSel('tabs.createPetition'));
    }

    public function testPetitionCantBeCreatedByNotSupporter()
    {
        $this->login('tester1@mail.ru', 'qwerty');
        usleep(150000);
        $this->open('mandate/index/details/1/');
        $this->waitForPresent($this->getCssSel('container'));
        usleep(50000);
        $this->assertElementNotPresent($this->getCssSel('tabs.createPetition'));
    }

    public function testPetitionCanBeCreated()
    {
        $this->login('vptester@mail.ru', 'qwerty');
        usleep(50000);
        $this->open('mandate/index/details/1/');

        $petition = $this->createPetition();

        $this->click($this->getCssSel('petitionsFeed.item.title'));
        usleep(50000);
        $this->assertActivePetition($petition);

        $petition = $this->createPetition();

        $this->click($this->getCssSel('petitionsFeed.item.title'));
        usleep(50000);
        $this->assertActivePetition($petition);
    }
    
    public function testPetitionCanBeSupported()
    {
        $this->login('vptester@mail.ru', 'qwerty');
        $this->open('mandate/index/details/1/');
        $this->assertPetitionsFeedPresent();

        //not supported yet
        $this->assertElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 0');
        $this->assertFalse($this->isElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen'));
        
        sleep(1);
        $this->click($this->getCssSel('petitionsFeed.item.supportBtn'));
        sleep(1);
        
        $this->waitForElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 1');
        $this->assertElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen');

        
        //switch to supported petition
        sleep(1);
        $this->click($this->getCssSel('petitionsFeed.item.title'));
        sleep(1);
        
        //check that it is supported
        $this->waitForElementContainsText($this->getCssSel('petitionDetails.active.support'), 'Petition supporters: 1');
        $this->assertElementHasClass($this->getCssSel('petitionDetails.active.supportBtn'), 'chosen');
        $this->waitForCssCount($this->getCssSel('petitionDetails.active.supporters.supporter'), 1);
        
        //unsupport
        sleep(1);
        $this->click($this->getCssSel('petitionDetails.active.supportBtn'));
        sleep(1);
        
        $this->waitForElementContainsText($this->getCssSel('petitionDetails.active.support'), 'Petition supporters: 0');
        $this->assertFalse($this->isElementHasClass($this->getCssSel('petitionDetails.active.supportBtn'), 'chosen'));
        $this->waitForCssCount($this->getCssSel('petitionDetails.active.supporters.supporter'), 0);
        
        //switch back to petitions feed
        sleep(1);
        $this->click($this->getCssSel('tabs.petitions'));
        sleep(1);
        
        //so it is unsupported now
        $this->waitForElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 0');
        $this->assertFalse($this->isElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen'));

        //support again
        sleep(1);
        $this->click($this->getCssSel('petitionsFeed.item.supportBtn'));
        sleep(1);
        
        $this->waitForElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 1');
        $this->assertElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen');

        //switch to opened tab with petition
        sleep(1);
        $this->click($this->getCssSel('tabs.tab') . ':nth-of-type(3)');
        sleep(1);
        
        $this->waitForElementContainsText($this->getCssSel('petitionDetails.active.support'), 'Petition supporters: 1');
        $this->assertElementHasClass($this->getCssSel('petitionDetails.active.supportBtn'), 'chosen');
        $this->waitForCssCount($this->getCssSel('petitionDetails.active.supporters.supporter'), 1);

        $this->logout();
        usleep(250000);
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('mandate/index/details/1/');
        $this->assertPetitionsFeedPresent();

        //already supported by previous user
        $this->waitForElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 1');
        $this->assertFalse($this->isElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen'));

        sleep(1);
        $this->click($this->getCssSel('petitionsFeed.item.supportBtn'));
        sleep(1);
        
        $this->waitForElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 2');
        $this->assertElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen');
    }

    public function testPetitionCantBeSupported()
    {
        $this->open('mandate/index/details/1/');
        $this->assertPetitionsFeedPresent();

        //not supported
        $this->assertElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 0');
        $this->assertFalse($this->isElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen'));

        sleep(1);
        $this->click($this->getCssSel('petitionsFeed.item.supportBtn'));
        sleep(1);
        
        $this->assertElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 0');
        $this->assertFalse($this->isElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen'));

        $this->login('tester1@mail.ru', 'qwerty');
        usleep(250000);
        $this->open('mandate/index/details/1/');
        $this->assertPetitionsFeedPresent();

        sleep(1);
        $this->click($this->getCssSel('petitionsFeed.item.supportBtn'));
        sleep(1);
        
        $this->assertElementContainsText($this->getCssSel('petitionsFeed.item.support'), 'Petition supporters: 0');
        $this->assertFalse($this->isElementHasClass($this->getCssSel('petitionsFeed.item.supportBtn'), 'chosen'));
    }

    public function testCanBeCommented()
    {
        $this->login('vptester@mail.ru', 'qwerty');
        
        $petitionId = 11;
        $petition = Petition::model()->findByPk($petitionId);

        $this->open('mandate/index/details/1/petition_' . $petitionId);        
        
        $this->assertPetitionsFeedPresent();
        
        $this->click("link=Discussion");
        $this->waitForVisible($this->getCssSel('petitionDetails.comments.newInput'));
        $this->type($this->getCssSel('petitionDetails.comments.newInputOpened'), "First message");
        $this->click($this->getCssSel('petitionDetails.comments.postBtn'));

        $this->waitForElementContainsText($this->getCssSel('petitionDetails.comments.item.text'), 'First message');
        
        $this->logout();
        usleep(50000);
        $this->open('mandate/index/details/1/petition_' . $petitionId);   
        $this->assertPetitionsFeedPresent();
        $this->click("link=Discussion");
        
        $this->waitForElementContainsText($this->getCssSel('petitionDetails.comments.item.text'), 'First message');
        usleep(5000);
        $this->assertNotVisible($this->getCssSel('petitionDetails.comments.newInput'));
    }
//    @todo:
//    testElectorsFilters
//    testElectorsShows
//    testPetitionsFilters

    protected function getCssSelectors($selector = null)
    {
        $selectors = array(
            'container' => '#mandate-details > div',
            'tabs'      => '#mandate-tabs > div.tabs-container > ul.nav-tabs',
            'tabs.tab'  => ' > li',
            'tabs.petitions' => ' > li:nth-of-type(2)',
            'tabs.createPetition' => ' > li > a[href^="#createPetition"]',
            'active'    => '.active',
            'petitionsFeed' => '#petitions-feed-container > div',
            'petitionsFeed.count' => ' a#items-count',
            'petitionsFeed.item'  => ' > .items > div',
            'petitionsFeed.item.title' => ' h4 > a',
            'petitionsFeed.item.support' => ' .support-count-info',
            'petitionsFeed.item.supportBtn' => ' .icon-thumbs-up',
            'petitionDetails'   => 'div[id^="petition"]',
            'petitionDetails.active' => '.active',
            'petitionDetails.active.support' => ' .support-count-info',
            'petitionDetails.active.supportBtn' => ' .icon-thumbs-up',
            'petitionDetails.comments' => ' div[^id="discussion"] > div',
            'petitionDetails.comments.item' => ' div.media.post', 
            'petitionDetails.comments.item.text' => ' .post-content', 
            'petitionDetails.comments.newInput' => ' div.comment-to-comment input',
            'petitionDetails.comments.newInputOpened' => ' div.comment-to-comment textarea',
            'petitionDetails.comments.postBtn' => ' button.post',
            'petitionDetails.active.supporters' => ' div[id^="supporters"] div.items',
            'petitionDetails.active.supporters.supporter' => ' div.user-info',
            'petition' => ' div.petition',
            'petition.details' => ' > .details',
            'petition.details.author' => ' div.span5 > h5 > a',
            'newPetitionForm' => 'form#petition-form'
        );

        if ($selector) {
            return $selectors[$selector];
        }

        return $selectors;
    }

    protected function createPetition()
    {
        $this->waitForPresent($this->getCssSel('container'));
        $this->assertElementPresent($this->getCssSel('tabs.createPetition'));

        $this->click($this->getCssSel('tabs.createPetition'));

        $this->waitForPresent($this->getCssSel('newPetitionForm'));

        $newPetitionTitle = 'New petition ' . time();
        $newPetitionText = $newPetitionTitle . ' text here.';

        $this->type($this->getCssSel('newPetitionForm') . ' input[name="Petition[title]"]', $newPetitionTitle);
        $this->type($this->getCssSel('newPetitionForm') . ' textarea', $newPetitionText);
        $this->click($this->getCssSel('newPetitionForm') . ' input.btn[type="submit"]');

        $this->waitForVisible($this->getCssSel('petitionsFeed'));
        $this->waitForElementContainsText($this->getCssSel('petitionsFeed.item.title'), $newPetitionTitle);

        $petition = Petition::model()->findByAttributes(array('title'=>$newPetitionTitle));

        return $petition;
    }


    protected function assertActivePetition($petition)
    {
        $this->waitForPresent($this->getCssSel('petitionDetails.active.petition.details'));
        usleep(100000);
        $this->assertLocation(TEST_BASE_URL . 'mandate/index/details/1/petition_' . $petition->id);
        $this->assertEquals($petition->title, $this->getText($this->getCssSel('tabs.tab') . '.active'));
        $this->assertElementContainsText($this->getCssSel('petitionDetails.active') . ' #petition-info p.text', $petition->content);
        $this->assertElementContainsText($this->getCssSel('petitionDetails.active.petition.details.author'), $petition->creator->username);
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

