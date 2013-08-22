<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class UserPageTest extends WebTestCase {
    
    public $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile'
    );
    
    protected function login() {
        $this->open('userAccount/login');
        $this->waitForPageToLoad("30000");
        $this->type("css=input#LoginForm_identity", "truvazia@gmail.com");
        $this->type("css=input#LoginForm_password.span5", "qwerty");
        $this->click("id=yw0");
        $this->waitForPageToLoad("30000");
    }
    
    protected function openOwnPage() {
        $this->login();
        $this->waitForElementPresent('css=div.media.post');
    }
            
    function testUsersPageDisplayed() {
        $this->open('userPage/1');
        $this->assertTextPresent('Vasiliy Pedak');
    }
    
    function testPostsAndCommentsRendered() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
	$this->assertCssCount('css=div.media.post', 5);
	$this->assertVisible('css=div.media.post:nth-of-type(1)');
        $this->assertVisible('css=div.media.post:nth-of-type(2)');
	$this->assertVisible('css=div.media.post:nth-of-type(3)');
        
	$this->assertVisible('css=.comments div.media.post:nth-of-type(1)');
	$this->assertVisible('css=.comments div.media.post:nth-of-type(2)');
    }
    
    function testOpacityOfRatesChangesWhenMousEnterersAndOuts() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        
        $this->assertEquals("0.25", $this->getEval("window.$('div.media.post .post-rate').css('opacity')"));
        $this->mouseOver("css=div.media.post .post-body");
        $this->assertEquals("1", $this->getEval("window.$('div.media.post .post-rate').css('opacity')"));
        $this->mouseOut("css=div.media.post .post-body");
        $this->assertEquals("0.25", $this->getEval("window.$('div.media.post .post-rate').css('opacity')"));
    }
    
    function testControlsNotShowsForUnauthorizedUser() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        
        $this->assertNotVisible('css=span.controls');
        
        $this->mouseOver("css=div.media.post .post-body");
        
        $this->assertNotVisible('css=span.controls');
    }
    
    function testAddingPostsAndCommentsUnavailableForUnauthorizedUser() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        
        $this->assertNotVisible('css=div.new-post');
    }
    
    function testControlsShowsForAuthorizedUser() {
        $this->openOwnPage();
        
        $this->assertNotVisible('css=span.controls');
        
        $this->mouseOver("css=div.media.post .post-body");
        
        $this->waitForVisible('css=span.controls');
    }
    
    function testAddingPostAndCommentsAvailableForAuthorizedUser() {
        $this->openOwnPage();
        
        $this->assertVisible('css=div.new-post');        
    }
    
    function testPostRemoves(){
        $this->openOwnPage();
        
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertTextPresent('11:42 AM 14 August, 2013');
        
        $this->mouseOver("css=div.media.post .post-body");
        $this->click("css=i.icon-remove");
        $this->assertTrue((bool)preg_match('/^You are going to delete the record\. Are you sure[\s\S]$/',$this->getConfirmation()));
        
        $this->waitForTextNotPresent('11:42 AM 14 August, 2013');
        $this->assertCssCount('css=div.media.post', 4);
    }
    
    function testCommentRemoves(){
        $this->openOwnPage();
        
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertTextPresent('7:46PM 8 August, 2013');
        
        $this->mouseOver("css=.comments div.media.post:nth-of-type(1) .post-body");
        $this->click("css=.comments div.media.post:nth-of-type(1) i.icon-remove");
        $this->assertTrue((bool)preg_match('/^You are going to delete the record\. Are you sure[\s\S]$/',$this->getConfirmation()));
        
        $this->waitForTextNotPresent('7:46PM 8 August, 2013');
        $this->assertCssCount('css=div.media.post', 4);        
    }
    
    function testEditBoxOpensOnFocusToAddPostInput(){
        $this->openOwnPage();
        
        $this->assertNotVisible('css=.new-post:nth-of-type(1) textarea');
        $this->assertNotVisible('css=.new-post:nth-of-type(1) .controls');
        
        $this->click("css=#add-post-top .new-post input");
        
        $this->waitForVisible('css=.new-post:nth-of-type(1) textarea');
        $this->waitForVisible('css=.new-post:nth-of-type(1) .controls');
        
        $this->fireEvent("css=#add-post-top .new-post textarea", "blur");
        
        $this->waitForNotVisible('css=.new-post:nth-of-type(1) textarea');
        $this->waitForNotVisible('css=.new-post:nth-of-type(1) .controls');
    }
    
    function testEditBoxOpensOnFocusToAddCommentInput(){
        $this->openOwnPage();
        
        $this->assertNotVisible('css=div.media.post:nth-of-type(1) .new-post textarea');
        $this->assertNotVisible('css=div.media.post:nth-of-type(1) .new-post .controls');
        
        $this->fireEvent("css=.media.post:nth-of-type(1) .new-post input", "focus");
        
        $this->waitForNotVisible("css=.media.post:nth-of-type(1) .new-post input");
        $this->waitForVisible('css=div.media.post:nth-of-type(1) .new-post textarea');
        $this->waitForVisible('css=div.media.post:nth-of-type(1) .new-post .controls');
        
        $this->fireEvent("css=.media.post:nth-of-type(1) .new-post textarea", "blur");
        
        $this->waitForNotVisible('css=div.media.post:nth-of-type(1) .new-post textarea');
        $this->waitForNotVisible('css=div.media.post:nth-of-type(1) .new-post .controls');        
    }
    
    function testEditBoxConfirmsCancel(){
        $this->openOwnPage();
        
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->click("css=#add-post-top .new-post input");
        $this->waitForVisible("css=#add-post-top textarea");
        $this->type("css=#add-post-top textarea", "Hello world!");
        $this->click("css=#add-post-top button:nth-child(2)");
        
        $this->waitForNotVisible('css=.new-post:nth-of-type(1) textarea');
        $this->waitForNotVisible('css=.new-post:nth-of-type(1) .controls');
        
        $this->assertCssCount('css=div.media.post', 5);
    }
    
    function testPostAdds(){
        $this->openOwnPage();
        
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->click("css=#add-post-top .new-post input");
        $this->waitForVisible("css=#add-post-top textarea");
        $this->type("css=#add-post-top textarea", "Hello world!");
        $this->click("css=#add-post-top button.post");
        
        $this->waitForNotVisible("css=#add-post-top textarea");
        
        $this->assertCssCount('css=div.media.post', 6);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(1)', 'Hello world!');        
    }
    
    function testCommentAdds(){
        $this->openOwnPage();
        
        $this->fireEvent("css=.media.post:nth-of-type(2) .new-post input", "focus");
        
        $this->waitForVisible("css=.media.post:nth-of-type(2) .new-post textarea");
        $this->type("css=.media.post:nth-of-type(2) .new-post textarea", "Hello world!");
        $this->click("css=.media.post:nth-of-type(2) button.post");
        
        $this->waitForNotVisible('css=div.media.post:nth-of-type(2) .new-post textarea');
        $this->waitForNotVisible('css=div.media.post:nth-of-type(2) .new-post .controls');
        
        $this->assertCssCount('css=div.media.post', 6);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .comments-feed div.media.post:nth-child(3)', 'Hello world!');
    }
    
    function testPostEdits() {
        $this->openOwnPage();
        
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .post-body");
        $this->click("css=div.media.post:nth-of-type(2) i.icon-pencil");
        
        $this->waitForNotVisible("css=div.media.post:nth-of-type(2)");
        $this->waitForVisible("css=#posts-feed > div div:nth-child(3) .new-post textarea");
        $this->type("css=#posts-feed > div div:nth-child(3) .new-post textarea", "Hello world!");
        $this->click("css=#posts-feed > div div:nth-child(3) .new-post button.post");
        
        //Checks that editBox closed
        $this->waitForCssCount('css=#posts-feed > div > div', 3);
        
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .post-body', 'Hello world!');
    }
    
    function testCommentEdits() {
        $this->openOwnPage();
        
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) .post-body");
        $this->click("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) i.icon-pencil");
        
        $this->waitForNotVisible("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2)");
        $this->waitForVisible("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post textarea");
        $this->type("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post textarea", "Hello world!");
        $this->click("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post button.post");
        
        //Checks that editBox closed
        $this->waitForCssCount('css=div.media.post:nth-of-type(2) .comments-feed > div', 2);
        
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) .post-body', 'Hello world!');
    }
    
    function testCantEditPostOfOtherUsers(){
        $this->openOwnPage();
        
        $this->mouseOver("css=div.media.post:nth-of-type(1) .post-body");
        
        $this->assertElementNotPresent('css=div.media.post:nth-of-type(1) span.controls .icon-pencil');
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(1) .post-body");
        
        $this->assertElementNotPresent('css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(1) span.controls .icon-pencil');
    }
    
    function testCanControlOwnPostsOnThePageOfOtherUser() {
        $this->openOwnPage();
        $this->open('userPage/2');
        $this->waitForElementPresent('css=div.media.post');
        
        //Can Edit
        
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .post-body");
        $this->click("css=div.media.post:nth-of-type(2) i.icon-pencil");
        
        $this->waitForNotVisible("css=div.media.post:nth-of-type(2)");
        $this->waitForVisible("css=#posts-feed > div div:nth-child(3) .new-post textarea");
        $this->type("css=#posts-feed > div div:nth-child(3) .new-post textarea", "Hello world!");
        $this->click("css=#posts-feed > div div:nth-child(3) .new-post button.post");
        
        //Checks that editBox closed
        $this->waitForCssCount('css=#posts-feed > div > div', 3);
        
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .post-body', 'Hello world!');
        
        //Can Remove
        
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertTextPresent('7:13 PM 8 August, 2013');
        
        $this->mouseOver("css=div.media.post:nth-child(2) .post-body");
        $this->click("css=div.media.post:nth-child(2) i.icon-remove");
        $this->assertTrue((bool)preg_match('/^You are going to delete the record\. Are you sure[\s\S]$/',$this->getConfirmation()));
        
        $this->waitForTextNotPresent('7:13 PM 8 August, 2013');
        //Removed with comments
        $this->assertCssCount('css=div.media.post', 2);
    }
    
    function testCanControlOwnCommentsOnThePageOfOtherUser() {
        $this->openOwnPage();
        $this->open('userPage/2');
        $this->waitForElementPresent('css=div.media.post');
        
        //Can Edit
        
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) .post-body");
        $this->click("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) i.icon-pencil");
        
        $this->waitForNotVisible("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2)");
        $this->waitForVisible("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post textarea");
        $this->type("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post textarea", "Hello world!");
        $this->click("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post button.post");
        
        //Checks that editBox closed
        $this->waitForCssCount('css=div.media.post:nth-of-type(2) .comments-feed > div', 2);
        
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) .post-body', 'Hello world!');
        
        //Can remove
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) .post-body");
        $this->click("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) i.icon-remove");
        $this->assertTrue((bool)preg_match('/^You are going to delete the record\. Are you sure[\s\S]$/',$this->getConfirmation()));
        
        $this->waitForCssCount('css=div.media.post', 4);
    }
    
    function testCantControlPostsOfOtherUsersOnPageOfOthers() {
        $this->openOwnPage();
        $this->open('userPage/2');
        $this->waitForElementPresent('css=div.media.post');
        
        $this->mouseOver("css=div.media.post:nth-of-type(1) .post-body");
        $this->assertNotVisible('css=span.controls');
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(1) .post-body");
        $this->assertNotVisible('css=span.controls');
        
    }
}
