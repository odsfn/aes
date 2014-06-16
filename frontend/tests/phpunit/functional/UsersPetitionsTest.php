<?php
class UsersPetitionsTest extends WebTestCase
{
    protected $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
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
        $this->open('userPage/petitions/1');
        $this->waitForPresent($this->getCssSel('container'));
        $this->waitForPresent($this->getCssSel('myPetitions.petitionsFeed'));
        $this->waitForPresent($this->getCssSel('petitionsForMe.petitionsFeed'));

        $this->assertPetitionsTotalCountIs($count = 15, 'petitionsForMe');
        $this->assertPetitionsOnPageCountIs($count, 'petitionsForMe');

        $this->assertPetitionsTotalCountIs($count = 16, 'myPetitions');
        $this->assertPetitionsOnPageCountIs($count, 'myPetitions');

        $petitions = Petition::model()->findAllByAttributes(array('creator_id'=>1));

        $this->assertPetitionsFeedContains($petitions, 'myPetitions');

        $petitions = Petition::model()->findAllByAttributes(array(
            'mandate_id' => Mandate::model()->getUsersMandates(1)
        ));

        $this->assertPetitionsFeedContains($petitions, 'petitionsForMe');

        $this->open('userPage/petitions/2');
        $this->waitForPresent($this->getCssSel('container'));
        $this->waitForPresent($this->getCssSel('petitionsFeed'));

        $this->assertElementNotPresent($this->getCssSel('myPetitions'));
        $this->assertElementNotPresent($this->getCssSel('petitionsForMe'));

        $this->assertPetitionsTotalCountIs(0);
        $this->assertElementContainsText($this->getCssSel('petitionsFeed.item'), 'There is no items.');
    }

    public function testPetitionsFilters()
    {
        $this->open('userPage/petitions/1');
        $this->waitForPresent($this->getCssSel('container'));
        $this->waitForPresent($this->getCssSel('myPetitions.petitionsFeed'));
        $this->waitForPresent($this->getCssSel('petitionsForMe.petitionsFeed'));

        $this->assertPetitionsTotalCountIs(16, 'myPetitions');

        $this->type('css=div[id^="myPetitions"] input[name="title"]', "еще");
        $this->click('css=div[id^="myPetitions"] input.form-submit');

        $this->assertPetitionsTotalCountIs($count = 2, 'myPetitions');
        $this->assertPetitionsOnPageCountIs($count, 'myPetitions');

        $this->click('css=div[id^="myPetitions"] input.form-reset');
        $this->assertPetitionsTotalCountIs(16, 'myPetitions');

        $this->type('css=div[id^="myPetitions"] input[name="title"]', "NOANY");
        $this->click('css=div[id^="myPetitions"] input.form-submit');

        $this->assertPetitionsTotalCountIs(0, 'myPetitions');
    }

    public function testPetitionCanBeSupported()
    {
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('userPage/petitions/1');
        $this->waitForPresent($this->getCssSel('container'));
        
        $cssSupportTextSel = 'css=#petitions div[id^="petitionsForMe"] div.items > div:nth-of-type(1) .support-count-info';
        $cssSupportBtnSel = 'css=#petitions div[id^="petitionsForMe"] div.items > div:nth-of-type(1) .icon-thumbs-up';
        $this->waitForPresent($cssSupportTextSel);
        
        sleep(1);
        
        //not supported yet
        $this->assertElementContainsText($cssSupportTextSel, 'Petition supporters: 0');
        $this->assertElementHasNoClass($cssSupportBtnSel, 'chosen');

        //support
        $this->click($cssSupportBtnSel);
        
        sleep(1);
        
        $this->waitForElementContainsText($cssSupportTextSel, 'Petition supporters: 1');
        $this->assertElementHasClass($cssSupportBtnSel, 'chosen');
        
        
        $this->logout();
        $this->open('userPage/petitions/1');
        $this->waitForPageToLoad("30000");
        $this->waitForPresent($this->getCssSel('container'));
        $this->waitForPresent($cssSupportTextSel);

        sleep(1);
        
        $this->waitForElementContainsText($cssSupportTextSel, 'Petition supporters: 1');
        $this->assertElementHasNoClass($cssSupportBtnSel, 'chosen');
        
//        $this->setSpeed(250);
        //can't be changed by not authorized
        $this->click($cssSupportBtnSel);
        
        sleep(1);
        
        $this->waitForElementContainsText($cssSupportTextSel, 'Petition supporters: 1');
        $this->assertElementHasNoClass($cssSupportBtnSel, 'chosen');

        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('userPage/petitions/1');
        $this->waitForPresent($this->getCssSel('container'));
        $this->waitForPresent($cssSupportTextSel);

        sleep(1);
        
        $this->waitForElementContainsText($cssSupportTextSel, 'Petition supporters: 1');
        $this->assertElementHasClass($cssSupportBtnSel, 'chosen');        
        
        //unsupport
        $this->click($cssSupportBtnSel);

        sleep(1);
        
        $this->waitForElementContainsText($cssSupportTextSel, 'Petition supporters: 0');
        $this->assertElementHasNoClass($cssSupportBtnSel, 'chosen');

    }

    protected function getCssSelectors($selector = null)
    {
        $selectors = array(
            'container' => '#petitions',
            'tabs'      => '> div.tabs-container > ul.nav-tabs',
            'tabs.tab'  => ' > li',
            'tabs.tab.forDeputy' => ':nth-of-type(2)',
            'tabs.tab.fromElectors' => ':nth-of-type(1)',
            'active'    => '.active',
            'myPetitions' => 'div[id^="myPetitions"]',
            'petitionsForMe' => 'div[id^="petitionsForMe"]',
            'petitionsFeed' => '> div',
            'petitionsFeed.count' => ' a#items-count',
            'petitionsFeed.item'  => ' > .items > div',
            'petitionsFeed.item.title' => ' h4 > a',
            'petitionsFeed.item.support' => ' .support-count-info',
            'petitionsFeed.item.supportBtn' => ' .icon-thumbs-up',
            'petition' => ' div.petition',
            'petition.details' => ' > .details',
            'petition.details.author' => ' div.span5 > h5 > a'
        );

        if ($selector) {
            return $selectors[$selector];
        }

        return $selectors;
    }

    protected function assertPetitionsTotalCountIs($count, $tab = null)
    {
        $sel = 'petitionsFeed.count';

        if ($tab) {
            $sel = $tab.'.'.$sel;
        }

        $this->waitForElementContainsText($this->getCssSel($sel), 'Found ' . $count);
    }

    protected function assertPetitionsOnPageCountIs($count, $tab = null)
    {
        if ($tab) {
            $cssSel = $this->getCssSel($tab) . ' ';
        } else {
            $cssSel = '';
        }

        $that = $this;
        $sel = $cssSel . $that->getCssSelectors('petitionsFeed') . $that->getCssSelectors('petitionsFeed.item');
        $this->waitFor(function() use(&$that, $count, $sel) {
            return ($count == $that->getCssCount($sel));
        });
    }

    protected function getFeedItemsCount($tab = null)
    {
        $cssSel = $this->getCssSel('petitionsFeed.item');

        if ($tab) {
            $cssSel = $this->getCssSel($tab) . ' '. $this->getCssSelectors('petitionsFeed') . $this->getCssSelectors('petitionsFeed.item');
        }

        return $this->getCssCount($cssSel);
    }

    protected function assertPetitionsFeedContains($petitions, $tab = null)
    {
        $notFound = array();
        $feedItemsCount = $this->getFeedItemsCount($tab);

        $headerTextCssSel = $this->getCssSel('petitionsFeed.item');

        if ($tab) {
            $headerTextCssSel = $this->getCssSel($tab) . $this->getCssSelectors('petitionsFeed') . $this->getCssSelectors('petitionsFeed.item');
        }

        for ($index = 1; $index <= $feedItemsCount; $index++) {

            $text = $this->getText($headerTextCssSel . ':nth-of-type(' . $index . ') h4 > a');

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

