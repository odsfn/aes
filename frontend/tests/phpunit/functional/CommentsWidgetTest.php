<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class CommentsWidgetTest extends WebTestCase {

    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
    );
    
    protected function login() {
        $this->open('userAccount/login');
        $this->waitForPageToLoad("30000");
        $this->type("css=input#LoginForm_identity", "truvazia@gmail.com");
        $this->type("css=input#LoginForm_password.span5", "qwerty");
        $this->click("id=yw0");
        $this->waitForPageToLoad("30000");
    }
    
    protected function openWidgetPage($login = true) {
        if($login)
            $this->login();
        
        $this->open('sandbox/play?view=commentsWidget');
        $this->waitForTextPresent('Lorem ipsum dolor. And comments to it below.');
    }
    
    protected function isInputPresent() {
        $inputPlaceholderSel = 'css=input[name="new-post"]';
        
        return ( $this->isElementPresent($inputPlaceholderSel) && $this->isVisible($inputPlaceholderSel) );
    }    
    
    public function testInputShows() {
        $this->openWidgetPage();
        $this->assertTrue($this->isInputPresent());
    }
    
    public function testInputNotShowsForUnauthorized() {
        $this->openWidgetPage(false);
        $this->assertFalse($this->isInputPresent());
    }
    
}
