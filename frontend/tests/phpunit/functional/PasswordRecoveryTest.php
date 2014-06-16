<?php

class PasswordRecoveryTest extends WebTestCase
{
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
    );
    
    function testRecoveryFormShows() {
        $this->open("/index-test.php");
        $this->click("link=Sign in");
        $this->waitForPageToLoad("30000");
        $this->click("link=exact:Lost password?");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent("Reset password"));
        $this->assertTrue($this->isTextPresent("Email"));
        $this->assertTrue($this->isTextPresent("Verification code"));
    }
    
    
}
