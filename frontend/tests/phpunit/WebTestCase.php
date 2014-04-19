<?php
/**
 * The base class for functional test cases.
 * In this class, we set the base URL for the test application.
 * We also provide some common methods to be used by concrete test classes.
 */
class WebTestCase extends CWebTestCase
{
	/**
	 * Sets up before each test method runs.
	 * This mainly sets the base URL for the test application.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->setBrowserUrl(TEST_BASE_URL);
	}
        
            
        protected function login($login, $pass) {
            $this->open('userAccount/login');
            $this->waitForPageToLoad("30000");
            $this->type("css=input#LoginForm_identity", $login);
            $this->type("css=input#LoginForm_password.span5", $pass);
            $this->click("id=yw0");
            $this->waitForPageToLoad("30000");
        }
        
        protected function checkSelectOptions($expected, $selector) {

            if(is_string($expected) && strstr($expected, ',') !== FALSE)
                $expected = explode(',', preg_replace ('/\s+/', '', $expected));
            elseif(is_string($expected))
                $expected = array($expected);

            $options = $this->getSelectOptions($selector);
            $this->assertCount(count($expected), $options);
            foreach ($expected as $expOption)
                $this->assertContains($expOption, $options);
        }
        
        protected function waitForPresent($elementSel, $time = 3000, $interval = 250) {
        
            for ($passedTime = 0; ; $passedTime+=$interval) {
                if ($passedTime >= $time) 
                    $this->fail("timeout");
                
                try {
                    if ($this->isElementPresent($elementSel)) break;
                } catch (Exception $e) {}
                    usleep($interval * 1000);
            }            
        }
}
