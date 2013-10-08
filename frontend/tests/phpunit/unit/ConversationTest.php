<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ConversationTest extends CDbTestCase {
    
    protected $fixtures = array(
        'conversation' => 'Conversation',
        'conversation_participant' => 'ConversationParticipant',
        'mesage'       => 'Message'
    );
    
    public function testFetchWithParticipant() {
        
        $usersConvsIds = array(1, 2, 3, 4, 5);
        
        $conversation = new Conversation();
        
        $conversation->criteriaWithParticipants(1);
        
        $result = $conversation->findAll();
        
        foreach ($result as $key => $conv) {
            $this->assertTrue(in_array($conv->id, $usersConvsIds));
        }
    }
}
