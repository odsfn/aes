<?php

class RegistrationTest extends WebTestCase
{    
    protected function setUp()
    {   
        parent::setUp();
        
        $this->truncateTables();
        
        $appLogger = $this->appLogger = Yii::app()->getComponent('log')->routes['info_log'];
        $logFilePath = $this->logFilePath = $appLogger->logPath . '/' . $appLogger->logFile;
        
        $this->deleteFile($logFilePath);
        
        $identifiersImagesDirPath = $this->identifiersImagesDirPath = Yii::getPathOfAlias('frontend.www') . Yii::app()->getModule('personIdentifier')->imagesDir;
//        self::removeDirectory($identifiersImagesDirPath);
    }
            
    function testRegistration() 
    {        
        $this->goToRegistration();
        $this->fillProfile();
        
        $this->type("id=PersonIdentifier_uploadingImage", $this->getFixtureFilePath());
        $this->type("id=PersonIdentifier_serialNumber", "1234");
        $this->type("id=PersonIdentifier_code", "4321");
        
        $this->click("id=yw1");
        
        $this->waitForPageToLoad("15000");

//        Check for flash message has been commented because it works unstable when we are using Selenium driver
//        $this->waitForPresent('css=div.flash-messages div.alert-success');
//        $this->assertTextPresent("We have created account especially for you! Please check your mail, and confirm registration");
//        $this->waitForTextPresent("We have created account especially for you! Please check your mail, and confirm registration");
        
        $this->activate();
        
        $this->checkLogin();
    }
    
    function testRegistrationWithAnotherIdentifierType()
    {
        $this->goToRegistration();
        $this->fillProfile();
        
        $this->switchIdentifier('anotherId');
        
        $this->type("id=PersonIdentifier_uploadingImage", $this->getFixtureFilePath());
        $this->type("id=PersonIdentifier_someField", "1234");
        $this->type("id=PersonIdentifier_anotherField", "4321");
        
        $this->click("id=yw1");
        
        $this->waitForPageToLoad("15000");

        $this->activate();
        
        $this->checkLogin();       
    }

    function testValidationWorks()
    {
        $this->goToRegistration();
        
        $this->click("id=yw1");
        
        $this->waitForPageToLoad();
        
        $this->assertTextPresent('Please fix the following input errors:');
        $this->assertTextPresent('Email cannot be blank.');
        $this->assertTextPresent('First Name cannot be blank.');
        $this->assertTextPresent('Last Name cannot be blank.');
        $this->assertTextPresent('Birth Place cannot be blank.');
        $this->assertTextPresent('Password cannot be blank.');
        $this->assertTextPresent('Password Check cannot be blank.');
        $this->assertTextPresent('Serial Number cannot be blank.');
        $this->assertTextPresent('Code cannot be blank.');
        $this->assertTextPresent('Scan copy or photo of document cannot be blank.');

        $this->fillProfile();
        
        $this->click("id=yw1");
        $this->waitForPageToLoad();
        
        $this->assertTextPresent('Please fix the following input errors:');
        $this->assertTextPresent('Serial Number cannot be blank.');
        $this->assertTextPresent('Code cannot be blank.');
        $this->assertTextPresent('Scan copy or photo of document cannot be blank.');        
        
        $this->switchIdentifier('anotherId');
        
        $this->click("id=yw1");
        $this->waitForPageToLoad();
        
        $this->assertTextPresent('Please fix the following input errors:');
        $this->assertTextPresent('Some Field cannot be blank.');
        $this->assertTextPresent('Another Field cannot be blank.');
        $this->assertTextPresent('Scan copy or photo of document cannot be blank.');        
        
        $this->type("id=PersonIdentifier_uploadingImage", $this->getFixtureFilePath());
        $this->type("id=PersonIdentifier_someField", "1234");
        $this->type("id=PersonIdentifier_anotherField", "4321");
        
        $this->click("id=yw1");
        
        $this->waitForPageToLoad("15000");

        $this->assertTextNotPresent('Please fix the following input errors:');
        
        $this->activate();
        
        $this->checkLogin();         
    }

    function testPassportRfIdent()
    {
        $this->goToRegistration();
        
        $this->fillProfile();
        
        $this->switchIdentifier(Yii::app()->getModule('personIdentifier')->personIdentifiers['passport_rf']['caption']);
        
        $this->assertTextPresent('Серия');
        $this->assertTextPresent('Номер');
        $this->assertTextPresent('Дата выдачи');
        $this->assertTextPresent('Орган, осуществивший выдачу');
        
        $this->type("id=PersonIdentifier_uploadingImage", $this->getFixtureFilePath());
        
        $this->click("css=button[type='submit']");
        $this->waitForPageToLoad();
        
        $this->assertTextPresent('Please fix the following input errors:');
        $this->assertTextPresent('Серия is invalid.');
        $this->assertTextPresent('Серия cannot be blank.');
        $this->assertTextPresent('Номер is invalid.');
        $this->assertTextPresent('Номер cannot be blank.');
        $this->assertTextPresent('The format of Дата выдачи is invalid.');
        $this->assertTextPresent('Дата выдачи cannot be blank.');
        $this->assertTextPresent('Орган, осуществивший выдачу cannot be blank.');

        $this->assertValue('id=PersonIdentifier_type', 'passport_rf');
        
        $this->type("id=PersonIdentifier_serial", "абвг");
        $this->type("id=PersonIdentifier_number", "1234");
        
        $this->click("id=PersonIdentifier_issued");
        
        $this->assertElementPresent('css=div.datepicker.datepicker-dropdown.dropdown-menu');
        $this->assertVisible('css=div.datepicker.datepicker-dropdown.dropdown-menu');
        $this->click("css=td.day.old");

        $this->click("id=PersonIdentifier_issuer");
//        $this->assertNotVisible('css=div.datepicker.datepicker-dropdown.dropdown-menu');
        
        $this->type("id=PersonIdentifier_issuer", "Отделом внутренних дел, такого-то города");
        
        $this->click("css=button[type='submit']");
        $this->waitForPageToLoad();
        
        $this->assertTextPresent('Please fix the following input errors:');
        $this->assertTextPresent('Серия is invalid.');
        $this->assertTextPresent('Номер is invalid.');
        
        $this->assertTextNotPresent('The format of Дата выдачи is invalid.');
        $this->assertTextNotPresent('Дата выдачи cannot be blank.');
        $this->assertTextNotPresent('Орган, осуществивший выдачу cannot be blank.');

        $this->type("id=PersonIdentifier_serial", "1234");
        $this->type("id=PersonIdentifier_number", "123456");
        $this->type("id=PersonIdentifier_uploadingImage", $this->getFixtureFilePath());
        
        $this->click("css=button[type='submit']");
        $this->waitForPageToLoad("15000");

        $this->assertTextNotPresent('Please fix the following input errors:');
        
        $this->activate();
        
        $this->checkLogin();        
    }
    
    protected function truncateTables()
    {
        Yii::app()->db->createCommand('SET foreign_key_checks = 0;')->execute();
        
        Yii::app()->db->createCommand('TRUNCATE user')->execute();
        Yii::app()->db->createCommand('TRUNCATE user_identity')->execute();
        Yii::app()->db->createCommand('TRUNCATE user_profile')->execute();
        Yii::app()->db->createCommand('TRUNCATE user_identity_confirmation')->execute();
        
        Yii::app()->db->createCommand('SET foreign_key_checks = 1;')->execute();        
    }
    
    protected function deleteFile($file)
    {
        if(file_exists($file)) {
            unlink($file);
        }
    }
    
    protected function getFixtureFilePath()
    {
        return Yii::getPathOfAlias('frontend.tests.phpunit.fixtures.common.files') . '/person-identity.jpg';
    }
    
    public static function removeDirectory($directory)
    {
        if (is_dir($directory)) {
            @chmod($directory, 0777);
        }
        
        $items=glob($directory.DIRECTORY_SEPARATOR.'{,.}*',GLOB_MARK | GLOB_BRACE);
        foreach($items as $item)
        {
            if(basename($item)=='.' || basename($item)=='..')
                continue;
            if(substr($item,-1)==DIRECTORY_SEPARATOR)
                self::removeDirectory($item);
            else {
                @chmod($item, 0777);
                unlink($item);
            }
        }
        if(is_dir($directory))
            rmdir($directory);
    }    
    
    protected function fillProfile()
    {   
        $this->type("id=RegistrationForm_password", "qwerty");
        $this->type("id=RegistrationForm_password_check", "qwerty");
        $this->type("id=RegistrationForm_email", "truvazia@gmail.com");
        $this->type("id=RegistrationForm_mobile_phone", "+1(234)567-89-98");
        $this->type("id=RegistrationForm_first_name", "Vasiliy");
        $this->type("id=RegistrationForm_last_name", "Pedak");
        $this->type("id=RegistrationForm_birth_place", "Ust-Katav, Cheliabinskaia oblast, Russia");
        $this->type("id=RegistrationForm_birthDayFormated", "08/27/2013");
        $this->select("id=RegistrationForm_gender", "label=Male");        
    }
    
    protected function goToRegistration()
    {
        $this->open("");
        $this->click("link=Registration");
        $this->waitForPageToLoad("30000");
    }

    protected function activate()
    {
        $this->waitForTextPresent('Log in');
        
        $logContents = file_get_contents($this->logFilePath);
        $matches = array();
        preg_match("/'activationUrl' => '(.+)'/", $logContents, $matches);
        $activationUrl = $matches[1];
        
        $this->open($activationUrl);
        $this->waitForPageToLoad("30000");
        $this->waitForTextPresent("Your account activated successfully. You can login now.");        
    }
    
    protected function checkLogin()
    {
        $this->open('userAccount/login');
        $this->waitForPageToLoad("30000");
        $this->type("css=input#LoginForm_identity", "truvazia@gmail.com");
        $this->type("css=input#LoginForm_password.span5", "qwerty");
        $this->click("id=yw0");
        
        $this->waitForTextPresent('Vasiliy Pedak');        
    }
    
    protected function switchIdentifier($type)
    {
        $this->select("id=PersonIdentifier_type", 'label=' . $type);
        
        $ident = new PersonIdentifier;
        
        $type = array_search($type, PersonIdentifier::getTypesCaptions());
        
        $ident->type = $type;
        $attrs = $ident->getTypeAttributeNames();
        
        foreach ($attrs as $attrName)
        {
            $this->waitForPresent('css=#person-identifier-fields #PersonIdentifier_' . $attrName);
        }      
    }
}
