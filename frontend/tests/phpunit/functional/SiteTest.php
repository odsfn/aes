<?php

class SiteTest extends WebTestCase
{
	public function testIndex()
	{
		$this->open('');
		$this->assertTextPresent('AES');
	}

//	public function testLoginLogout()
//	{
//		$this->open('');
//		// ensure the user is logged out
//		if($this->isTextPresent('Log out'))
//			$this->clickAndWait('link=Log out');
//
//		// test login process, including validation
//		$this->clickAndWait('link=Sign in');
//		$this->assertElementPresent('name=LoginForm[username]');
//		$this->type('name=LoginForm[username]','demo');
//		$this->click("//input[@value='Login']");
//		$this->waitForTextPresent('Password cannot be blank.');
//		$this->type('name=LoginForm[password]','demo');
//		$this->clickAndWait("//input[@value='Login']");
//		$this->assertTextNotPresent('Password cannot be blank.');
//		$this->assertTextPresent('Logout');
//
//		// test logout process
//		$this->assertTextNotPresent('Login');
//		$this->clickAndWait('link=Logout (demo)');
//		$this->assertTextPresent('Login');
//	}
}
