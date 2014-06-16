<?php

class PersonIdentifierTest extends CDbTestCase
{
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier'
    );
    
    public function testGetSerializedAttribute()
    {
        $pi = Profile::model()->findByPk(1)->personIdentifier;
        $this->assertEquals(1104, $pi->serial);
        $this->assertTrue(isset($pi->serial));
    }
}

