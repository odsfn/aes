<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PetitionTest extends CDbTestCase {
    
    public $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'election'     => 'Election',
        'mandate'      => array('Mandate', 'unit/petition/mandate'),
//        'elector'    => array('Elector', 'unit/electionProcess/elector'),
        'vote'       => array('Vote', 'unit/petition/vote')
    );
    
    public function testCanCreateAdherentOnly() {
       $petition = new Petition;
       $petition->title = 'Some petition';
       $petition->content = 'Petition content';       
       $petition->mandate_id = 1;
       $petition->creator_id = 2;
       
       $this->assertNull($petition->created_ts);
       
       $this->assertTrue($petition->save());
       
       $format = 'Y-m-d';
       
       $expDate = new DateTime;
       $actualDate = new DateTime($petition->created_ts);
       
       $this->assertEquals($expDate->format($format), $actualDate->format($format));
    }
    
    public function testThrowsExceptionWhenPetitionCreatedByNotAdherent() {
       $this->setExpectedException('PetitionException', 'Petition can be created by mandate\'s adherents only');

       $petition = new Petition;
       $petition->title = 'Some petition';
       $petition->content = 'Petition content';       
       $petition->mandate_id = 1;
       $petition->creator_id = $creator_id = 1;

       $this->assertFalse($petition->save(false));
    }
    
    public function testValidationFailsWhenPetitionCreatedByNotAdherent() {
       $petition = new Petition;
       $petition->title = 'Some petition';
       $petition->content = 'Petition content';
       $petition->mandate_id = 1;
       $petition->creator_id = $creator_id = 1;

       $this->assertFalse($petition->save());
       $this->assertContains('Petition can be created by mandate\'s adherents only', $petition->getErrors('creator_id'), 'Actual errors: ' . print_r($petition->getErrors('creator_id'), true));
    }
}
