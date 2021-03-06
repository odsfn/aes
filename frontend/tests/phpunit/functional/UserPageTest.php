<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class UserPageTest extends WebTestCase {
    
    public $fixtures = array(
        'target' => ':target',
        'user' => 'userAccount.models.UserAccount',
        'user_identity' => 'userAccount.models.Identity',
        'user_profile' => 'userAccount.models.Profile',
        'personIdentifier' => 'personIdentifier.models.PersonIdentifier',
        'post'         => 'Post',
        'post_rate'      => 'PostRate'
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
        usleep(100000);
        $this->waitForElementPresent('css=div.media.post');
        usleep(75000);
    }
            
    function testUsersPageDisplayed() {
        $this->open('userPage/1');
        $this->assertTextPresent('Vasiliy Pedak');
    }
    
    function testPostsAndCommentsRendered() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        usleep(50000);
	$this->assertCssCount('css=div.media.post', 5);
	$this->assertVisible('css=div.media.post:nth-of-type(1)');
        $this->assertVisible('css=div.media.post:nth-of-type(2)');
	$this->assertVisible('css=div.media.post:nth-of-type(3)');
        
	$this->assertVisible('css=.comments div.media.post:nth-of-type(1)');
	$this->assertVisible('css=.comments div.media.post:nth-of-type(2)');
    }
    
    function testOpacityOfRatesChangesWhenMouseEntersAndOuts() {
        $this->openOwnPage();
        $this->waitForElementPresent('css=div.media.post');
        usleep(50000);
        $this->assertEquals("0.25", $this->getEval("window.$('div.media.post .post-rate span').css('opacity')"));
        $this->mouseEnter("css=div.media.post .post-body .rate-control");
        usleep(50000);
        $this->assertEquals("1", $this->getEval("window.$('div.media.post .post-rate span').css('opacity')"));
        $this->mouseLeave("css=div.media.post .post-body .rate-control");
        usleep(50000);
        $this->assertEquals("0.25", $this->getEval("window.$('div.media.post .post-rate span').css('opacity')"));
    }
    
    function testControlsNotShowsForUnauthorizedUser() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        usleep(50000);
        $this->assertNotVisible('css=span.controls');
        
        $this->mouseOver("css=div.media.post .post-body");
        usleep(50000);
        $this->assertNotVisible('css=span.controls');
    }
    
    function testAddingPostsAndCommentsUnavailableForUnauthorizedUser() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        usleep(50000);
        $this->assertNotVisible('css=div.new-post');
    }
    
    function testControlsShowsForAuthorizedUser() {
        $this->openOwnPage();
        usleep(50000);
        $this->assertNotVisible('css=span.controls');
        
        $this->mouseOver("css=div.media.post .post-body");
        
        $this->waitForVisible('css=span.controls');
    }
    
    function testAddingPostAndCommentsAvailableForAuthorizedUser() {
        $this->openOwnPage();
        usleep(50000);
        $this->assertVisible('css=div.new-post');        
    }
    
    function testPostRemoves(){
        $this->openOwnPage();
        usleep(150000);
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertTextPresent('Aug 14, 2013 11:42:00 AM');
        $this->assertElementContainsText('css=#posts-counter-cont' ,'7 records');
        
        $this->mouseOver("css=div.media.post .post-body");
        usleep(50000);
        $this->click("css=i.icon-remove");
        usleep(50000);
        $this->assertTrue((bool)preg_match('/^You are going to delete the record\. Are you sure[\s\S]$/',$this->getConfirmation()));
        
        $this->waitForTextNotPresent('Aug 14, 2013 11:42:00 AM');
        $this->waitForElementNotPresent('css=div.loadmask');
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 4);
        $this->assertElementContainsText('css=#posts-counter-cont' ,'6 records');
    }
    
    function testCommentRemoves(){
        $this->openOwnPage();
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertTextPresent('Aug 08, 2013 07:46:00 PM');
        
        $this->mouseOver("css=.comments div.media.post:nth-of-type(1) .post-body");usleep(50000);
        $this->click("css=.comments div.media.post:nth-of-type(1) i.icon-remove");usleep(50000);
        $this->assertTrue((bool)preg_match('/^You are going to delete the record\. Are you sure[\s\S]$/',$this->getConfirmation()));
        
        $this->waitForTextNotPresent('Aug 08, 2013 07:46:00 PM');
        $this->assertCssCount('css=div.media.post', 4);        
    }
    
    function testEditBoxOpensOnFocusToAddPostInput(){
        $this->openOwnPage();
        usleep(50000);
        $this->assertNotVisible('css=.new-post:nth-of-type(1) textarea');
        $this->assertNotVisible('css=.new-post:nth-of-type(1) .controls');
        
        $this->click("css=#add-post-top .new-post input");
        usleep(50000);
        $this->waitForVisible('css=.new-post:nth-of-type(1) textarea');
        $this->waitForVisible('css=.new-post:nth-of-type(1) .controls');
        
        $this->fireEvent("css=#add-post-top .new-post textarea", "blur");
        usleep(50000);
        $this->waitForNotVisible('css=.new-post:nth-of-type(1) textarea');
        $this->waitForNotVisible('css=.new-post:nth-of-type(1) .controls');
    }
    
    function testEditBoxOpensOnFocusToAddCommentInput(){
        $this->openOwnPage();
        usleep(50000);
        $this->assertNotVisible('css=div.media.post:nth-of-type(1) .new-post textarea');
        $this->assertNotVisible('css=div.media.post:nth-of-type(1) .new-post .controls');
        
        $this->fireEvent("css=.media.post:nth-of-type(1) .new-post input", "focus");
        usleep(50000);
        $this->waitForNotVisible("css=.media.post:nth-of-type(1) .new-post input");
        $this->waitForVisible('css=div.media.post:nth-of-type(1) .new-post textarea');
        $this->waitForVisible('css=div.media.post:nth-of-type(1) .new-post .controls');
        
        $this->fireEvent("css=.media.post:nth-of-type(1) .new-post textarea", "blur");
        usleep(50000);
        $this->waitForNotVisible('css=div.media.post:nth-of-type(1) .new-post textarea');
        $this->waitForNotVisible('css=div.media.post:nth-of-type(1) .new-post .controls');        
    }
    
    function testEditBoxConfirmsCancel(){
        $this->openOwnPage();
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->click("css=#add-post-top .new-post input");
        $this->waitForVisible("css=#add-post-top textarea");
        $this->type("css=#add-post-top textarea", "Hello world!");
        $this->click("css=#add-post-top button:nth-child(2)");
        
        $this->waitForNotVisible('css=.new-post:nth-of-type(1) textarea');
        $this->waitForNotVisible('css=.new-post:nth-of-type(1) .controls');
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
    }
    
    function testPostAdds(){
        $this->openOwnPage();
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->assertElementContainsText('css=#posts-counter-cont' ,'7 records');
        
        $this->click("css=#add-post-top .new-post input");
        $this->waitForVisible("css=#add-post-top textarea");
        $this->type("css=#add-post-top textarea", "Hello world!");
        $this->click("css=#add-post-top button.post");
        
        $this->waitForNotVisible("css=#add-post-top textarea");
        
        $this->assertCssCount('css=div.media.post', 6);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(1)', 'Hello world!');
        
        $this->assertElementContainsText('css=#posts-counter-cont' ,'8 records');
    }
    
    function testCommentAdds(){
        $this->openOwnPage();
        usleep(50000);
        $this->fireEvent("css=.media.post:nth-of-type(2) .new-post input", "focus");
        
        $this->waitForVisible("css=.media.post:nth-of-type(2) .new-post textarea");
        $this->type("css=.media.post:nth-of-type(2) .new-post textarea", "Hello world!");
        $this->click("css=.media.post:nth-of-type(2) button.post");
        
        $this->waitForNotVisible('css=div.media.post:nth-of-type(2) .new-post textarea');
        $this->waitForNotVisible('css=div.media.post:nth-of-type(2) .new-post .controls');
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 6);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .comments-feed div.media.post:nth-child(3)', 'Hello world!');
    }
    
    function testPostEdits() {
        $this->openOwnPage();
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .post-body");
        $this->click("css=div.media.post:nth-of-type(2) i.icon-pencil");
        
        $this->waitForNotVisible("css=div.media.post:nth-of-type(2)");
        $this->waitForVisible("css=#posts-feed > div div:nth-child(3) .new-post textarea");
        $this->type("css=#posts-feed > div div:nth-child(3) .new-post textarea", "Hello world!");
        $this->click("css=#posts-feed > div div:nth-child(3) .new-post button.post");
        
        //Checks that editBox closed
        $this->waitForCssCount('css=#posts-feed > div > div', 3);
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .post-body', 'Hello world!');
    }
    
    function testCommentEdits() {
        $this->openOwnPage();
        usleep(50000);
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
        usleep(50000);
        $this->mouseOver("css=div.media.post:nth-of-type(1) .post-body");
        usleep(50000);
        $this->assertElementNotPresent('css=div.media.post:nth-of-type(1) span.controls .icon-pencil');
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(1) .post-body");
        usleep(50000);
        $this->assertElementNotPresent('css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(1) span.controls .icon-pencil');
    }
    
    function testCanControlOwnPostsOnThePageOfOtherUser() {
        $this->openOwnPage();
        $this->open('userPage/2');
        $this->waitForElementPresent('css=div.media.post');
        usleep(50000);
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
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .post-body', 'Hello world!');
        
        //Can Remove
        
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertTextPresent('Aug 08, 2013 07:13:00 PM');
        
        $this->mouseOver("css=div.media.post:nth-child(2) .post-body");
        $this->click("css=div.media.post:nth-child(2) i.icon-remove");
        usleep(50000);
        $this->assertTrue((bool)preg_match('/^You are going to delete the record\. Are you sure[\s\S]$/',$this->getConfirmation()));
        
        $this->waitForTextNotPresent('Aug 08, 2013 07:13:00 PM');
        //Removed with comments
        $this->assertCssCount('css=div.media.post', 2);
    }
    
    function testCanControlOwnCommentsOnThePageOfOtherUser() {
        $this->openOwnPage();
        $this->open('userPage/2');
        $this->waitForElementPresent('css=div.media.post');
        
        //Can Edit
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) .post-body");
        $this->click("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) i.icon-pencil");
        
        $this->waitForNotVisible("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2)");
        $this->waitForVisible("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post textarea");
        $this->type("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post textarea", "Hello world!");
        $this->click("css=div.media.post:nth-of-type(2) .comments-feed > div:nth-child(3) .new-post button.post");
        
        //Checks that editBox closed
        $this->waitForCssCount('css=div.media.post:nth-of-type(2) .comments-feed > div', 2);
        usleep(50000);
        $this->assertCssCount('css=div.media.post', 5);
        $this->assertElementContainsText('css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) .post-body', 'Hello world!');
        
        //Can remove
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) .post-body");
        $this->click("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(2) i.icon-remove");
        usleep(50000);
        $this->assertTrue((bool)preg_match('/^You are going to delete the record\. Are you sure[\s\S]$/',$this->getConfirmation()));
        
        $this->waitForCssCount('css=div.media.post', 4);
    }
    
    function testCantControlPostsOfOtherUsersOnPageOfOthers() {
        $this->openOwnPage();
        $this->open('userPage/2');
        $this->waitForElementPresent('css=div.media.post');
        
        $this->mouseOver("css=div.media.post:nth-of-type(1) .post-body");usleep(5000);
        $this->assertElementNotPresent('css=div.media.post:nth-of-type(1) .post-body span.controls i');
        
        $this->mouseOver("css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(1) .post-body");usleep(5000);
        $this->assertElementNotPresent('css=div.media.post:nth-of-type(2) .comments div.media.post:nth-of-type(1) .post-body span.controls i');
        
    }

    function testPressOnMoreButtonAppendsPosts() {
        $this->openOwnPage();
        usleep(500000);
        $this->waitForElementPresent('css=div.media.post');
        $this->assertElementContainsText('css=span.posts-count', '7');
        
        $this->assertCssCount('css=#posts-feed > div > div.media.post', 3);
        
        $this->assertTextNotPresent('Post (4|5|6|7)');
        
        $this->assertVisible('css=div.get-more');
        $this->click("css=div.get-more > div");
        
        $this->waitForVisible('css=div.get-more span .loader');
        $this->waitForCssCount('css=#posts-feed > div > div.media.post', 6);
        
        usleep(50000);
        
        $this->assertVisible('css=div.get-more');
        $this->assertVisible('css=div.get-more a');
        $this->assertNotVisible('css=div.get-more span .loader');
        
        $this->assertTextPresent('Post 4');
        $this->assertTextPresent('Post 5');
        $this->assertTextPresent('Post 6');
        
        $this->assertTextNotPresent('Post 7');
        
        $this->click("css=div.get-more > div");
        $this->waitForCssCount('css=#posts-feed > div > div.media.post', 7);
        
        //will be hidden when we got to the end
        $this->click("css=div.get-more > div");
        $this->waitForNotVisible('css=div.get-more');
        
        $this->assertTextPresent('Post 4');
        $this->assertTextPresent('Post 5');
        $this->assertTextPresent('Post 6');
        $this->assertTextPresent('Post 7');
    }
    
    function testPressUsersFilter() {
        $this->openOwnPage();
        usleep(50000);
        $this->assertElementContainsText('css=span.posts-count', '7');
        
        $this->assertTextPresent('Jhon Lenon');
        $this->assertTextPresent('Vasiliy Pedak');
        $this->assertElementContainsText('css=small.author-switcher a', "Show users' records only");
        
        $this->click('css=small.author-switcher a');
        
        $this->assertElementPresent('css=div.loadmask');
        $this->waitForElementNotPresent('css=div.loadmask');
        
        $this->assertElementContainsText('css=span.posts-count', '2');
        
        $this->assertTextNotPresent('Jhon Lenon');
        $this->assertTextPresent('Vasiliy Pedak');
        
        $this->assertElementContainsText('css=small.author-switcher a', "Show all records");
        
        $this->click('css=small.author-switcher a');
        $this->assertElementPresent('css=div.loadmask');
        $this->waitForElementNotPresent('css=div.loadmask');
        
        $this->assertElementContainsText('css=span.posts-count', '7');
        
        $this->assertTextPresent('Jhon Lenon');
        $this->assertTextPresent('Vasiliy Pedak');
        $this->assertElementContainsText('css=small.author-switcher a', "Show users' records only");
    }
    
    function testNotAuthUsersCantRatePosts() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        usleep(50000);
        $this->assertElementNotPresent('css=.post-rate span.chosen');
        
        $this->assertElementContainsText('css=span.icon-thumbs-up', '0');
        
        $this->click('css=span.icon-thumbs-up');
        usleep(50000);
        $this->assertElementContainsText('css=span.icon-thumbs-up', '0');
    }
    
    function testAuthUserCanRatePosts() {
        $this->openOwnPage();
        usleep(50000);
        $this->assertElementContainsText('css=.post-rate:first span.icon-thumbs-up', '0');
        
        $this->click('css=.post-rate:first span.icon-thumbs-up');
        $this->waitForElementPresent('css=.post-rate:first span.icon-thumbs-up.chosen');
        $this->assertElementContainsText('css=.post-rate:first span.icon-thumbs-up', '1');
        
        
        $this->assertElementContainsText('css=.post-rate:first span.icon-thumbs-down', '0');
        
        $this->click('css=.post-rate:first span.icon-thumbs-down');
        $this->waitForElementPresent('css=.post-rate:first span.icon-thumbs-down.chosen');
        $this->assertElementNotPresent('css=.post-rate:first span.icon-thumbs-up.chosen');
        $this->assertElementContainsText('css=.post-rate:first span.icon-thumbs-up', '0');
        $this->assertElementContainsText('css=.post-rate:first span.icon-thumbs-down', '1');
        
        $this->click('css=.post-rate:first span.icon-thumbs-down');
        $this->waitForElementNotPresent('css=.post-rate:first span.icon-thumbs-down.chosen');
        $this->assertElementContainsText('css=.post-rate:first span.icon-thumbs-down', '0');
    }
    
    function testOrder() {
        $this->openOwnPage();
        usleep(50000);
        $datesOrderAll = array(
            'Aug 14, 2013 11:42:00 AM', 'Aug 08, 2013 07:13:00 PM', 'Aug 08, 2013 10:42:00 AM',
            'Aug 07, 2013 10:12:00 AM', 'Aug 07, 2013 10:08:00 AM', 'Aug 07, 2013 10:05:00 AM',
            'Aug 07, 2013 10:00:00 AM'
        );
        
        $datesOrderUsersOnly = array(
            'Aug 08, 2013 07:13:00 PM', 'Aug 08, 2013 10:42:00 AM'
        );
        
        $this->loadMore();
        $this->loadMore();
        
        $this->checkDatesOrder($datesOrderAll);
        
        $this->click('css=small.author-switcher a');
        $this->waitForElementNotPresent('css=div.loadmask');
        
        $this->checkDatesOrder($datesOrderUsersOnly);
        
        $this->click('css=small.author-switcher a');
        $this->waitForElementNotPresent('css=div.loadmask');
        
        $this->loadMore();
        $this->loadMore();
        
        $this->checkDatesOrder($datesOrderAll);
    }
    
    function testOrderAfterAdd(){
        $this->openOwnPage();
        usleep(50000);
        $this->click("css=#add-post-top .new-post input");
        $this->waitForVisible("css=#add-post-top textarea");
        $this->type("css=#add-post-top textarea", $addedText = "Hello world! " . time());
        $this->click("css=#add-post-top button.post");
        
        $this->waitForNotVisible("css=#add-post-top textarea");
        usleep(5000);
        $this->assertElementContainsText('css=#posts-feed > div > div.media.post:nth-child(1)', $addedText);
        
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        usleep(5000);
        $this->assertElementContainsText('css=#posts-feed > div > div.media.post:nth-child(1)', $addedText);
    }
    
    function testRateShows() {
       $this->openOwnPage();
       usleep(5000);
       $elementSelector = 'css=#posts-feed > div > div.media.post:nth-child(2) > .media-body > .post-body > .post-after';
       
       $this->assertElementContainsText("$elementSelector .icon-thumbs-up", "3");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "2");
       
       $this->assertElementPresent("$elementSelector .icon-thumbs-up.chosen");
       
       $commentSelector ='css=#posts-feed > div > div.media.post:nth-child(2) .comments .media.post > .media-body > .post-body > .post-after';
       
       $this->assertElementContainsText("$commentSelector .icon-thumbs-up", "0");
       $this->assertElementContainsText("$commentSelector .icon-thumbs-down", "2");
       
       $this->assertElementNotPresent("$commentSelector .icon-thumbs-up.chosen");
    }
    
    function testRateAddPositive() {
       $this->openOwnPage();
       usleep(50000);
       $elementSelector = 'css=#posts-feed > div > div.media.post:nth-child(1) > .media-body > .post-body > .post-after';
       
       $this->assertElementContainsText("$elementSelector .icon-thumbs-up", "0");
       $this->assertElementNotPresent("$elementSelector .icon-thumbs-up.chosen");
       
       $this->click("$elementSelector .icon-thumbs-up");
       
       $this->waitForElementPresent("$elementSelector .icon-thumbs-up.chosen");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-up", "1");
    }
    
    function testRateAddNegative() {
       $this->openOwnPage();
       usleep(50000);
       $elementSelector = 'css=#posts-feed > div > div.media.post:nth-child(1) > .media-body > .post-body > .post-after';
       
       $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "0");
       $this->assertElementNotPresent("$elementSelector .icon-thumbs-down.chosen");
       
       $this->click("$elementSelector .icon-thumbs-down");
       
       $this->waitForElementPresent("$elementSelector .icon-thumbs-down.chosen");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "1");
    }
    
    function testRateRemovePositive() {
       $this->openOwnPage();
       usleep(50000);
       $elementSelector = 'css=#posts-feed > div > div.media.post:nth-child(2) > .media-body > .post-body > .post-after';
       
       $this->assertElementContainsText("$elementSelector .icon-thumbs-up", "3");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "2");
       
       $this->assertElementPresent("$elementSelector .icon-thumbs-up.chosen");
       
       $this->click("$elementSelector .icon-thumbs-up");
       
       $this->waitForElementNotPresent("$elementSelector .chosen");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-up", "2");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "2");
    }
    
    function testRateChangesByClickingOnOposite() {
       $this->openOwnPage();
       usleep(50000);
       $elementSelector = 'css=#posts-feed > div > div.media.post:nth-child(2) > .media-body > .post-body > .post-after';
       
       $this->assertElementContainsText("$elementSelector .icon-thumbs-up", "3");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "2");
       
       $this->assertElementPresent("$elementSelector .icon-thumbs-up.chosen");
       
       $this->click("$elementSelector .icon-thumbs-down");
       
       $this->waitForElementNotPresent("$elementSelector .icon-thumbs-up.chosen");
       $this->waitForElementPresent("$elementSelector .icon-thumbs-down.chosen");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-up", "2");
       $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "3");  
    }
    
//    function testRateRemoveNegative() {}
    
    function testRateCantBeChangedByUnauthorized() {
        $this->open('userPage/1');
        $this->waitForElementPresent('css=div.media.post');
        usleep(50000);
        $elementSelector = 'css=#posts-feed > div > div.media.post:nth-child(1) > .media-body > .post-body > .post-after';
        
        $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "0");

        $this->click("$elementSelector .icon-thumbs-down");
        sleep(1);
        $this->assertElementNotPresent("$elementSelector .icon-thumbs-down.chosen");
        $this->assertElementContainsText("$elementSelector .icon-thumbs-down", "0");
    }

    
    protected function checkDatesOrder($orderedDates) {
        foreach ($orderedDates as $index => $value) {
            $this->assertElementContainsText('css=#posts-feed > div > div.media.post:nth-child(' . ($index+1) . ') h5.media-heading', $value);
        }
    }
    
    protected function loadMore() {
        $this->click("css=div.get-more > div");
        $this->waitForElementNotPresent('css=div.loadmask');
    }
}
