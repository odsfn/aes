<?php

class RegistrationTest extends WebTestCase
{    
    function testRegistration() {
        
        Yii::app()->db->createCommand('SET foreign_key_checks = 0;')->execute();
        
        Yii::app()->db->createCommand('TRUNCATE user')->execute();
        Yii::app()->db->createCommand('TRUNCATE user_identity')->execute();
        Yii::app()->db->createCommand('TRUNCATE user_profile')->execute();
        Yii::app()->db->createCommand('TRUNCATE user_identity_confirmation')->execute();
        
        Yii::app()->db->createCommand('SET foreign_key_checks = 1;')->execute();
        
        $appLogger = Yii::app()->getComponent('log')->routes['info_log'];
        $logFilePath = $appLogger->logPath . '/' . $appLogger->logFile;
        
        if(file_exists($logFilePath)) {
            unlink($logFilePath);
        }
        
        $this->open("");
        $this->click("link=Registration");
        $this->waitForPageToLoad("30000");
        $this->type("id=RegistrationForm_password", "qwerty");
        $this->type("id=RegistrationForm_password_check", "qwerty");
        $this->type("id=RegistrationForm_email", "truvazia@gmail.com");
        $this->type("id=RegistrationForm_mobile_phone", "+1(234)567-89-98");
        $this->type("id=RegistrationForm_first_name", "Vasiliy");
        $this->type("id=RegistrationForm_last_name", "Pedak");
        $this->type("id=RegistrationForm_birth_place", "Ust-Katav, Cheliabinskaia oblast, Russia");
        $this->type("id=RegistrationForm_birthDayFormated", "08/27/2013");
        
        $this->select("id=RegistrationForm_gender", "label=Male");
        
        $this->click("id=yw1");
        
        $this->waitForPageToLoad("15000");

//        Check for flash message has been commented because it works unstable when we are using Selenium driver
//        $this->waitForPresent('css=div.flash-messages div.alert-success');
//        $this->assertTextPresent("We have created account especially for you! Please check your mail, and confirm registration");
//        $this->waitForTextPresent("We have created account especially for you! Please check your mail, and confirm registration");
        
        $this->waitForTextPresent('Log in');
        
        $logContents = file_get_contents($logFilePath);
        $matches = array();
        preg_match("/'activationUrl' => '(.+)'/", $logContents, $matches);
        $activationUrl = $matches[1];
        
        $this->open($activationUrl);
        $this->waitForPageToLoad("30000");
        $this->waitForTextPresent("Your account activated successfully. You can login now.");
        
        $this->open('userAccount/login');
        $this->waitForPageToLoad("30000");
        $this->type("css=input#LoginForm_identity", "truvazia@gmail.com");
        $this->type("css=input#LoginForm_password.span5", "qwerty");
        $this->click("id=yw0");
        
        $this->waitForTextPresent('Vasiliy Pedak');
    }
}
