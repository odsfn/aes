<?php
/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class MessagingTest extends WebTestCase
{

    public $fixtures = array(
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'conversation' => 'Conversation',
        'conversation_participant' => 'ConversationParticipant',
        'message' => 'Message',
    );

    protected function getCssSelectors()
    {
        return array(
            'container' => '#column-right > div:nth-child(1)',
            'tabs' => '> ul.nav',
            'tabs.conversations' => ' a[href="#conversations-tab"]',
            'tabs.activeConv' => ' a[href="#active-conv-tab"]',
            'moreBtn' => 'div.get-more',
            'feedItemsCounter' => 'a#convs-count',
            'feed' => 'div#convs-container > div',
            'feed.conv' => ' > div',
            'chatTabs' => 'ul.convs-tabs-cntr',
            'chatTabs.tab' => ' > li',
            'chatTabs.active' => ' > li.active',
            'chat' => 'div.active-chat-cnt > div.active',
            'chat.newPost' => ' div.new-post',
            'chat.newPost.participant2Img' => ' div.participant:last-of-type img',
            'chat.newPost.input' => ' textarea',
            'chat.newPost.sendBtn' => ' button.post',
            'chat.messagesCount' => ' a.msgs-count',
            'chat.message' => ' div.messages-cnt > div > div.media.post'
        );
    }    
    
    public function testMyMessagesPageLoadsWithEmptyConversationsList()
    {
        Yii::app()->db->createCommand('SET foreign_key_checks = 0; '
                . 'TRUNCATE TABLE conversation; '
                . 'TRUNCATE TABLE conversation_participant; '
                . 'TRUNCATE TABLE message; '
                . 'SET  foreign_key_checks = 1;'
        )->execute();
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->click('link=My messages');

        $this->waitForConversationsCount(0);
    }

    public function testWriteMessage()
    {
        Yii::app()->db->createCommand('SET foreign_key_checks = 0; '
                . 'TRUNCATE TABLE conversation; '
                . 'TRUNCATE TABLE conversation_participant; '
                . 'TRUNCATE TABLE message; '
                . 'SET  foreign_key_checks = 1;'
        )->execute();
        
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('messaging/index/chat_with/2');

        $this->assertConversationsContainerShown();

        $this->assertActiveChatWith('Another User');

        $this->assertCanWriteMessage($msg1 = 'Hello, how are you?', 'Vasiliy Pedak');
        $this->assertCanWriteMessage($msg2 = 'Message 2', 'Vasiliy Pedak');
        
        //Checks that shows this conversation afer opening all messages
        $this->open('messaging/index');
        $this->sleep(50);
        $this->assertConversationsContainerShown();
        $this->waitForConversationsCount(1);
        $this->sleep(250);
        //Open this conversation
        $this->click($this->getCssSel('feed.conv') . ':nth-of-type(1) div.post-content');
        $this->sleep(500);
        $this->assertActiveChatWith('Another User');
        $this->sleep(250);
        $this->assertTextPresent($msg1);
        $this->assertTextPresent($msg2);
        
        $this->assertCanWriteMessage($msg3 = 'Message 3', 'Vasiliy Pedak');
        
        $this->assertTextPresent($msg1);
        $this->assertTextPresent($msg2);
        $this->assertTextPresent($msg3);
        
        //Check that we can open existing conversation with user
        $this->open('messaging/index/chat_with/2');
        $this->assertActiveChatWith('Another User');
    }

    public function testSwapsConversationsToChats()
    {
        $this->login('truvazia@gmail.com', 'qwerty');
        usleep(50000);        
        $this->open('messaging/index');
        
        $this->assertConversationsContainerShown();
        
        $this->assertMessagesCountMenuIndicator(4); //check messages count on menu
        
        $this->assertEquals('Another User', $this->getConversation(0)->with);
        $this->assertTrue($this->getConversation(0)->isUnviewed);
        
        $this->selectConversation(0);
        $this->assertActiveChatWith('Another User');
        
        $this->assertMessagesCountMenuIndicator(3); //check messages count on menu
        
        $this->swapToConversations();
        
        //Check that list resorted and unviewed marker were hidden
        $this->assertEquals('Another User', $this->getConversation(3)->with);
        $this->assertFalse($this->getConversation(3)->isUnviewed);
        
        $this->assertMessagesCountMenuIndicator(3); //check messages count on menu
        
        $this->selectConversation(0);
        $this->assertActiveChatWith('Jhon Lenon');
        
        $this->assertMessagesCountMenuIndicator(2); //check messages count on menu
    }
    
    //check swapping and closing chats
    public function testSwappingAndClosingChats()
    {
        $this->login('truvazia@gmail.com', 'qwerty');
        $this->open('messaging/index');
        
        $this->assertConversationsContainerShown();
        
        //open all conversations
        $this->selectConversation(0);
        $this->assertActiveChatWith('Another User');
        
        $this->swapToConversations();
        
        $this->selectConversation(0);
        $this->assertActiveChatWith('Jhon Lenon');
        
        $this->swapToConversations();
        
        $this->selectConversation(0);
        $this->assertActiveChatWith('Yetanother User');        
        
        $this->swapToConversations();
        
        $this->selectConversation(0);
        $this->assertActiveChatWith('Steve Jobs');        
        
        $this->assertTextPresent('Cnvs_text_5.');
        $this->assertTextPresent('Sep 21, 2013 11:19:33 AM');
        
        $this->clickConvHeader(3);
        
        $this->assertActiveChatWith('Steve Jobs');        
        
        $this->assertElementContainsText($this->getCssSel('chat'), 'Cnvs_text_5.');
        $this->assertElementContainsText($this->getCssSel('chat'), 'Sep 21, 2013 11:19:33 AM');
        
        $this->clickConvHeader(0);
        $this->assertActiveChatWith('Another User');
        $this->assertElementContainsText($this->getCssSel('chat'), 'Cnvs_text_1.');
        $this->assertElementContainsText($this->getCssSel('chat'), 'Sep 25, 2013 11:19:33 AM');
        $this->assertElementNotContainsText($this->getCssSel('chat'), 'Cnvs_text_5.');
        
        $this->assertCssCount($this->getCssSel('chatTabs.tab'), 4);
        
        $this->closeConv(0);
        
        $this->assertCssCount($this->getCssSel('chatTabs.tab'), 3);
        
        $this->assertActiveChatWith('Jhon Lenon');
        
        $this->clickConvHeader(2);
        $this->assertActiveChatWith('Steve Jobs'); 
        
        $this->closeConv(1);
        
        $this->assertCssCount($this->getCssSel('chatTabs.tab'), 2);
        
        $this->assertActiveChatWith('Steve Jobs');
        
        $this->clickConvHeader(0);
        
        $this->assertActiveChatWith('Jhon Lenon');
        $this->assertElementContainsText($this->getCssSel('chat'), 'Cnvs_text_2.');
        
        //trying to open active opened conversation from conversation list
        $this->swapToConversations();
        $this->selectConversation(1);
        $this->assertActiveChatWith('Jhon Lenon');
        $this->assertCssCount($this->getCssSel('chatTabs.tab'), 2);
        
        //trying to open not active opened conversation from conversation list
        $this->swapToConversations();
        $this->selectConversation(3);
        $this->assertActiveChatWith('Steve Jobs');
        $this->assertCssCount($this->getCssSel('chatTabs.tab'), 2);
        
        $this->closeConv(1);
        $this->assertCssCount($this->getCssSel('chatTabs.tab'), 1);
        
        $this->assertActiveChatWith('Jhon Lenon');
        $this->closeConv(0);
        $this->assertCssCount($this->getCssSel('chatTabs.tab'), 0);
        $this->assertNotVisible($this->getCssSel('tabs.activeConv'));
        
        $this->assertVisible($this->getCssSel('tabs.conversations'));
        
        //check that chat opens again
        $this->selectConversation(0);
        $this->assertVisible($this->getCssSel('tabs.activeConv'));
        $this->assertCssCount($this->getCssSel('chatTabs.tab'), 1);
    }

    //check chatting
    public function testChatting()
    {
        $this->login('truvazia@gmail.com', 'qwerty');
        usleep(50000);
        $this->open('messaging/index/chat_with/2');

        $this->assertConversationsContainerShown();

        $this->assertActiveChatWith('Another User');
        
        $this->waitForMessagesCount(1);
        $this->assertStringStartsWith('Cnvs_text_1.', $this->getMessage(0)->text);
        
        $message = new Message;
        $message->conversation_id = 1;
        $message->user_id = 2;
        $message->text = 'Hello, Vasiliy!';
        $message->save();
        
        $this->waitForMessagesCount(2, 21000);
        $this->assertElementContainsText($this->getCssSel('chat'), 'Hello, Vasiliy!');
        
        //@TODO:
        //check gets incoming message in opened conversation
        //check gets incoming message in not opened conversation
        //check gets incoming message in new conversation
        //check new messages count indicator changes
    }

    //check conversations fitlering
    
    //check conversations listing
    
    //check messages loading
    
    //check unread messages indicator in the chat title
    protected function clickConvHeader($index)
    {
        $this->click($this->getCssSel('chatTabs.tab') . ':nth-of-type(' . ($index+1) . ') > a');
        usleep(50000);
    }    
    
    protected function waitForConversationsCount($count)
    {
        $this->assertConversationsContainerShown();

        $this->waitForElementContainsText($this->getCssSel('feedItemsCounter'), 'Found ' . $count . ' conversations');

        if ($count == 0) {
            usleep(50000);
            $this->assertElementContainsText($this->getCssSel('feed'), 'No items found.');
        } else {
            $this->waitForCssCount($this->getCssSel('feed.conv'), $count);
        }
    }

    protected function assertConversationsContainerShown()
    {
        $this->waitForPresent($this->getCssSel('container'));
        $this->waitForPresent($this->getCssSel('tabs.conversations'));
        $this->waitForPresent($this->getCssSel('moreBtn'));
    }

    protected function assertChatTabIsActive()
    {
        $this->waitForPresent($this->getCssSel('tabs.activeConv'));
        $this->waitForVisible($this->getCssSel('tabs.activeConv'));
        $this->waitForPresent($this->getCssSel('chatTabs.active'));
        usleep(5000);
        $this->assertElementHasClass($this->getCssSel('chatTabs.active'), 'active');
    }

    protected function assertActiveChatWith($userName)
    {
        $this->assertChatTabIsActive();

        $this->assertElementContainsText($this->getCssSel('chatTabs.active'), $userName);

        $this->assertElementAttributeEquals($this->getCssSel('chat.newPost.participant2Img'), 'alt', $userName);
    }

    protected function assertCanWriteMessage($msg, $from)
    {
        $currentCount = $this->getCssCount($this->getCssSel('chat.message'));
        $expCount = $currentCount+1;
        
        $this->writeMsg($msg);

        $this->waitForMessagesCount($expCount);
        usleep(5000);
        $this->assertEquals($msg, $this->getMessage($currentCount)->text);
        $this->assertEquals($from, $this->getMessage($currentCount)->author);
    }

    protected function writeMsg($msg)
    {
        $this->type($this->getCssSel('chat.newPost.input'), $msg);
        $this->click($this->getCssSel('chat.newPost.sendBtn'));
        usleep(50000);
    }

    protected function waitForMessagesCount($count, $time = 3000)
    {
        $this->waitForElementContainsText($this->getCssSel('chat.messagesCount'), 'Total Messages Count: ' . $count, $time);
        $this->waitForCssCount($this->getCssSel('chat.message'), $count);
    }
    
    protected function getMessage($index)
    {
        $message = new stdClass();
        
        $sel = $this->getCssSel('chat.message') . ':nth-of-type(' . ($index+1) . ')';
        $textSel = $sel . ' div.post-content';
        $authorSel = $sel . ' span.user';
        
        $message->text = $this->getText($textSel);
        $message->author = $this->getText($authorSel);
        
        return $message;
    }
    
    protected function getConversation($index)
    {
        $conv = new stdClass();
        
        $index++;
        $sel = $this->getCssSel('feed.conv') . ':nth-of-type(' . $index . ') div.media.post';
        $selWith = $sel . ' span.user';
        $selText = $sel . ' div.post-content';
        $selBell = $sel . ' .media-heading > i.icon-bell';
        
        $conv->with = $this->getText($selWith);
        $conv->text = $this->getText($selText);
        $conv->isUnviewed = $this->isVisible($selBell);
        
        return $conv;
    }

    protected function selectConversation($index)
    {
        $index++;
        $sel = $this->getCssSel('feed.conv') . ':nth-of-type(' . $index . ') div.media.post div.post-content';
        $this->click($sel);
        $this->sleep(250);
    }
    
    protected function swapToConversations()
    {
        $this->click($this->getCssSel('tabs.conversations'));
        usleep(50000);
        $this->assertVisible($this->getCssSel('feed'));
        $this->assertNotVisible($this->getCssSel('chat'));
    }

    protected function assertMessagesCountMenuIndicator($expCount)
    {
        $actualCount = $this->getMessagesCountMenuIndicator();
        usleep(50000);
        $this->assertEquals($expCount, $actualCount);
    }
    
    protected function getMessagesCountMenuIndicator()
    {
        $count = $this->getText('css=div#column-left div#navigation li.messaging > a > span');
        
        $count = str_replace('+', '', $count);
        
        return $count;
    }
    
    protected function closeConv($index)
    {
        $this->click($this->getCssSel('chatTabs.tab') . ':nth-of-type(' . ($index+1) . ') > a > i.icon-remove');
        usleep(50000);
    }
}