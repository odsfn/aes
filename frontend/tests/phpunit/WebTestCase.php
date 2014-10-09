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
//            if($this->assertElementNotPresent("css=a#sign-in")) {
//                $this->logout();
//            }
            
            $this->open('userAccount/login');
            $this->waitForPageToLoad("30000");
            $this->type("css=input#LoginForm_identity", $login);
            $this->type("css=input#LoginForm_password.span5", $pass);
            $this->click("id=yw0");
            $this->waitForPageToLoad("30000");
        }
        
        protected function logout()
        {
            $this->open('userAccount/login/out');
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
        
        protected function waitForPresent($elementSel, $time = 5000, $interval = 250) {
        
            for ($passedTime = 0; ; $passedTime+=$interval) {
                if ($passedTime >= $time) 
                    $this->fail("timeout");
                
                try {
                    if ($this->isElementPresent($elementSel)) break;
                } catch (Exception $e) {}
                usleep($interval * 1000);
            }            
        }
        
        protected function waitFor($callback, $message = 'timeout', $time = 5000, $interval = 250) {
            
            for ($passedTime = 0; ; $passedTime+=$interval) {
                if ($passedTime >= $time) 
                    $this->fail($message);
                
                try {
                    if ($callback() === true) break;
                } catch (Exception $e) {}
                usleep($interval * 1000);
            }            
            
        }

        protected function waitForNotPresent($elementSel, $time = 3000, $interval = 250)
        {
            $that = $this;
            $callback = function() use($elementSel, $that) {
                return !$that->isElementPresent($elementSel);
            };
            
            $this->waitFor($callback, 'timeout', $time, $interval);
        }

        protected function waitForCssCount($elementSel, $count, $time = 5000, $interval = 250)
        {
            $that = $this;
            $callback = function() use($that, $elementSel, $count) {
                return $that->getCssCount($elementSel) == $count;
            };
            $this->waitFor($callback, 'timeout', $time, $interval);
        }


        protected function waitForElementContainsText($elementSel, $text, $time = 5000, $interval = 250) {
            $text = '';
            for ($passedTime = 0; ; $passedTime+=$interval) {
                if ($passedTime >= $time) 
                    $this->fail("Timeout. Last text is '$text' ");
                
                try {
                    if (strpos($text = $this->getText($elementSel), $text) !== false) break;
                } catch (Exception $e) {}
                    usleep($interval * 1000);
            }            
        }
        
        protected function mouseEnter($selector) {
//            $this->fireEvent($selector, 'mouseenter');
            $this->triggerJqueryEvent($selector, 'mouseenter');
        }
        
        protected function mouseLeave($selector) {
//            $this->fireEvent($selector, 'mouseleave');
            $this->triggerJqueryEvent($selector, 'mouseleave');
        }
        
        protected function triggerJqueryEvent($selector, $event) {
            if(strpos($selector, 'css=') === FALSE)
                throw new Exception ('Only css selectors supported by mouseEnter method');
            
            $selector = str_replace('css=', '', $selector);
            
            $this->runScript('$("' . $selector . '").trigger("' . $event . '");');
        }
        
        public function isElementHasClass($sel, $targetClass) {
            
            $class = $this->getAttribute($sel, 'class');
            
            if (!$class || $class === '')
                return false;
            
            $classes = AESHelper::explode($class, ' ');
            
            if (is_string($targetClass)) {
                $targetClasses = array($targetClass);
            }elseif (is_array($targetClass)){
                $targetClasses = $targetClass;
            } 
            
            foreach ($targetClasses as $targetClass) {
                if (!in_array($targetClass, $classes)) {
                    return false;
                }
            }
            
            return true;
        }
        
        protected function assertElementHasClass($sel, $targetClass)
        {
            $this->assertTrue($this->isElementHasClass($sel, $targetClass));
        }
        
        protected function assertElementHasNoClass($sel, $targetClass)
        {
            $this->assertFalse($this->isElementHasClass($sel, $targetClass));
        }

        protected function waitForElementHasNoClass($sel, $targetClass, $time = 5000, $interval = 250)
        {
            $that = $this;
            $callback = function() use ($that, $sel, $targetClass) {
                return !$that->isElementHasClass($sel, $targetClass);
            };
            $this->waitFor($callback, $message = 'Timeout', $time, $interval);
        }
        
//      @todo: fix it
//        protected function waitForElementHasNoClass($sel, $targetClass)
//        {
//            $that = $this;
//            $this->waitFor(function() use(&$that, $sel, $targetClass) {
//                return ($that->isElementHasClass($sel, $targetClass) === false);
//            });
//        }

        protected function getAttribute($sel, $attr) 
        {
            try {
                $attr = parent::getAttribute($sel . '@' . $attr);
            } catch (Exception $ex) {
                $attr = false;
            }
            
            return $attr;
        }
        
        protected function getCssSel($index, $selectors = null)
        {
            if(!$selectors)
                $sels = $this->getCssSelectors();

            $resultSel = 'css=' . $sels['container'];

            if ($index !== 'container') {

                $sel = '';

                if (strstr($index, '.')) {
                    $path = explode('.', $index);

                    foreach ($path as $item) {
                        if (isset($sels[$item])) {
                            $sel .= $sels[$item];
                        }
                    }

                }

                $sel .= $sels[$index];

                $resultSel .= ' ' . $sel;
            }

            return $resultSel;
        } 
        
        /**
         * Prepares selection to use in selecnium assertions. 
         * Adds "css=" prefix if not present
         * Replaces {%key%} by values from $params 
         * @param string $selection css selector
         * @param array $params
         */
        protected function parseSel($selection, $params = array())
        {
            if(strpos($selection, 'css=') === FALSE)
                $selection = 'css=' . $selection;
            
            if(count($params)) {
                $handler = function($matches) use($params) {
                    return $params[$matches[1]];
                };

                $selection = preg_replace_callback(
                    '/{%(.+)%}/', $handler, $selection
                );
            }
            
            return $selection;
        }


        protected function assertElementAttributeEquals($sel, $attr, $value)
        {
            $attrValue = $this->getAttribute($sel, $attr);
            $this->assertEquals($value, $attrValue);
        }
        
        protected function sleep($microseconds)
        {
            usleep($microseconds * 1000);
        }
}
