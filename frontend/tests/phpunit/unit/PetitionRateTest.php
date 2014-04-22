<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PetitionRateTest extends CDbTestCase {
    
    public $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'petition'     => 'Petition',
        'petition_rate'     => 'PetitionRate',
        'election'     => 'Election',
        'mandate'      => array('Mandate', 'unit/petition/mandate'),
//        'elector'    => array('Elector', 'unit/electionProcess/elector'),
        'vote'       => array('Vote', 'unit/petition/vote')
    );
    
    public function testCanRateMandatesAdherentOnly() {
       $petition = new Petition;
       $petition->title = 'Some petition';
       $petition->content = 'Petition content';        
       $petition->mandate_id = 1;
       $petition->creator_id = 2; 
       $petition->save();
       
       $petitionRate = new PetitionRate;
       $petitionRate->target_id = $petition->id;
       $petitionRate->user_id = 2;
       $petitionRate->score = PetitionRate::SCORE_POSITIVE;
       
       $this->assertTrue($petitionRate->save());
    }
    
    public function testThrowsExceptionWhenPetitionRatedByNotMandatesAdherent() {
       $this->setExpectedException('PetitionRateException', 'Petition can be rated by mandate\'s adherents only');

       $petition = new Petition;
       $petition->title = 'Some petition';
       $petition->content = 'Petition content';        
       $petition->mandate_id = 1;
       $petition->creator_id = 2;

       $this->assertTrue($petition->save());
       
       $pRate = new PetitionRate;
       $pRate->target_id = $petition->id;
       $pRate->user_id   = 1;
       $pRate->score = PetitionRate::SCORE_POSITIVE;
       
       $this->assertFalse($pRate->save(false));
    }

    public function testValidationFailsWhenRateCreatedByNotAdherent() {
       $petition = new Petition;
       $petition->title = 'Some petition';
       $petition->content = 'Petition content';        
       $petition->mandate_id = 1;
       $petition->creator_id = 2;

       $this->assertTrue($petition->save());
       
       $pRate = new PetitionRate;
       $pRate->target_id = $petition->id;
       $pRate->user_id   = 1;
       $pRate->score = PetitionRate::SCORE_POSITIVE;
       
       $this->assertFalse($pRate->save());
       $this->assertContains('Petition can be rated by mandate\'s adherents only', $pRate->getErrors('user_id'), 'Actual errors: ' . print_r($petition->getErrors('user_id'), true));
    }
}
