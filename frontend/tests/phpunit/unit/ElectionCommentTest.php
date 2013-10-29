<?php
Yii::import('common.extensions.restfullyii.components.MorrayBehavior');
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionCommentTest extends CDbTestCase {
    
    protected $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'election' => 'Election',
        'election_comment' => 'ElectionComment',
    );
    
    public function testFetchWithUser() {
        
        $electionComment = new ElectionComment;
        
        $criteria = $electionComment->with(array(
            'user' => array(
                'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
            )
        ));
        
        $result = $criteria->findAll();
        
        foreach($result as $index => $model)
        {
            if(!array_key_exists('MorrayBehavior', $model->behaviors()))
                $model->attachBehavior('MorrayBehavior', new MorrayBehavior());
            
            $result[$index] = $model->toArray();
        }        
        
        $this->assertCount(5, $result);
        
        $this->assertEquals('Vasiliy Pedak', $result[0]['user']['displayName']);
    }
}