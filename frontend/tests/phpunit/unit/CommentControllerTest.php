<?php
Yii::import('frontend.modules.api.controllers.CommentController');
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class CommentControllerTest extends CDbTestCase { 
    
    protected $verbose = false;
    
    protected $showResult = false;
    
    protected $fixtures = array(
        'user' => 'userAccount.models.UserAccount',
        'user_profile' => 'userAccount.models.Profile',
        'election_comment' => 'ElectionComment',
        'election' => 'Election',
        'election_auth_assignment' => ':election_auth_assignment',
        'AuthAssignment'   => ':AuthAssignment'
    );

    protected function getCookiePath() {
        return Yii::app()->basePath.'/runtime/cookie.txt';
    }
    
    protected function xhr($path, $body = '', $method = 'POST', $withCookie = false) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, TEST_BASE_URL . $path);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept" => "application/json, text/javascript, */*; q=0.01",
            "Accept-Language" => "ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
            "Accept-Encoding" => "gzip, deflate",
            "Cache-Control"   => "no-cache",
            "Connection"      => "keep-alive",
            "Content-Type"    => "application/json; charset=UTF-8",
            "Pragma"          => "no-cache",
            "User-Agent"      => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:20.0) Gecko/20100101 Firefox/20.0",
            "X-Requested-With"=> "XMLHttpRequest"
        ));
        
        curl_setopt($ch, CURLOPT_VERBOSE, $this->verbose);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if($withCookie)
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->getCookiePath());
        
        if($body)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        
        $result=curl_exec($ch);
        
        if($this->showResult)
            echo $result;      
        
        curl_close($ch);
        
        return $result;
    }
    
    protected function authenticate($login, $pass) {
        
        $ch = curl_init();
        
        if(strtolower((substr($url,0,5))=='https')) { // если соединяемся с https
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
        
        curl_setopt($ch, CURLOPT_URL, TEST_BASE_URL . 'userAccount/login');

        curl_setopt($ch, CURLOPT_VERBOSE, $this->verbose);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"LoginForm[identity]=".$login."&LoginForm[password]=".$pass);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:20.0) Gecko/20100101 Firefox/20.0");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        //сохранять полученные COOKIE в файл
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->getCookiePath());
        $result=curl_exec($ch);
        
        // Убеждаемся что произошло перенаправление после авторизации
        if(preg_match('~Location: (.+)userPage/index~m', $result) == false)
            /*return false; */die('Curl authontication failed: login incorrect');

        curl_close($ch);
        
        return true;
    }

    public function testAuthenticate() {
        $this->assertTrue($this->authenticate('truvazia@gmail.com', 'qwerty'));
    }    
    
    public function testUnauthorizedCanRead() {
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET');
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET');
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }

    public function testUnauthorizedCanReadWithUnassignedLevelRead() {
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_READ;
        $election1->save();
        
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET');
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET');
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }
    
    public function testUnauthorizedCanReadWithUnassignedLevelCreate() {
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_COMMENT;
        $election1->save();
        
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET');
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET');
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }    
    
    public function testUnauthorizedCantReadWithUnassignedLevelNone() {
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_NONE;
        $election1->save();
        
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET');
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 3\d\d~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET');
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 3\d\d~m', $result));
    }

    public function testAuthorizedUnassignedCanReadWithUnassignedLevelRead() {
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_READ;
        $election1->save();
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }

    public function testAuthorizedUnassignedCantReadWithUnassignedLevelNone() {
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_NONE;
        $election1->save();
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 403~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 403~m', $result));        
    }
    
    public function testAuthorizedAssignedCanReadWithUnassignedLevelNone() {
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_NONE;
        $election1->save();
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $election1->assignRoleToUser(1, 'election_participant');
        
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));        
    }

    public function testUnauthorizedCantCreate() {
        $result = $this->xhr('api/Election_comment', '{"target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}');
        
        //assert redirect
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 3\d\d~m', $result));
    }
    
    public function testUnauthorizedCantUpdate() {
        $result = $this->xhr('api/Election_comment/1', 
                '{"id":"1","target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}',
                'PUT'        
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 3\d\d~m', $result));
    }
    
    public function testUnauthorizedCantDelete() {
        $result = $this->xhr('api/Election_comment/1', 
                '{"id":"1","target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}',
                'DELETE'      
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 3\d\d~m', $result));
    }

    public function testAuthorizedCanCreate() {
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment', '{"target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}', 'POST', true);
        
        //assert created
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));   
    }
    
    public function testAuthorizedUnassignedCantCreateWithUnassignedLevelRead() {
        
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_READ;
        $election1->save();
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment', '{"target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}', 'POST', true);        
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 403~m', $result)); 
    }

    public function testAuthorizedUnassignedCantCreateWithUnassignedLevelNone() {
        
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_NONE;
        $election1->save();
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment', '{"target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}', 'POST', true);        
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 403~m', $result)); 
    }
    
    public function testAuthorizedAssignedCanCreateWithUnassignedLevelNotCreate() {
        
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_READ;
        $election1->save();
        
        $election1->assignRoleToUser(1, 'election_participant');
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment', '{"target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}', 'POST', true);        
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result)); 
        
    }
    
    public function testAuthorizedCanUpdateOwn() {
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment/1', 
                '{"id":"1","target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment 1. Edited.","likes":null,"dislikes":null,"comments":[]}',
                'PUT',
                true
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 2\d\d~m', $result));
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        $this->assertEquals('Comment 1. Edited.', ElectionComment::model()->findByPk(1)->content);
    }
    
    public function testAuthorizedCantUpdateNotOwn() {
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment/4', 
                '{"id":"4","target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment 4. Edited.","likes":null,"dislikes":null,"comments":[]}',
                'PUT',
                true
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 403~m', $result));       
    }
    
    public function testAuthorizedCanDeleteOwn() {
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment/1', 
                '',
                'DELETE',
                true
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 2\d\d~m', $result));
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }
    
    public function testAuthorizedCantDeleteNotOwn() {
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment/4', 
                '',
                'DELETE',
                true
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 403~m', $result));          
    }
    
    public function testAdminCanDeleteNotOwn() {
        $this->authenticate('vptester@mail.ru', 'qwerty');
        
        $result = $this->xhr('api/Election_comment/1', 
                '',
                'DELETE',
                true
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 2\d\d~m', $result));
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        // for other target
        
        $this->authenticate('truvazia@gmail.com', 'qwerty');
        
        $result = $this->xhr('api/Election_comment/5', 
                '',
                'DELETE',
                true
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 2\d\d~m', $result));
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }
    
    public function testAdminCantDeleteNotOwnInNotAssignedCommentable() {
        $this->authenticate('vptester@mail.ru', 'qwerty');
        
        $result = $this->xhr('api/Election_comment/5', 
                '',
                'DELETE',
                true
        );
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.\d 403~m', $result));       
    }

    public function testAdminCanRead() {
        
        $this->authenticate('vptester@mail.ru', 'qwerty');
        
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }    
    
    public function testAdminCanReadWithUnassignedLevelNone() {
        
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_NONE;
        $election1->save();
        
        $this->authenticate('vptester@mail.ru', 'qwerty');
        
        $result = $this->xhr('api/Election_comment?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
        $result = $this->xhr('api/Election_comment/1?filter[target_id]=1', '', 'GET', true);
        
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }
    
    public function testAdminCanCreate() {
        
        $this->authenticate('vptester@mail.ru', 'qwerty');
        
        $result = $this->xhr('api/Election_comment', '{"target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}', 'POST', true);
        
        //assert created
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
        
    }    

    public function testAdminCanCreateWithUnassignedLevelNone() {
                
        $election1 = Election::model()->findByPk(1);
        $election1->unassigned_access_level = Election::UNASSIGNED_CAN_NONE;
        $election1->save();
        
        $this->authenticate('vptester@mail.ru', 'qwerty');
        
        $result = $this->xhr('api/Election_comment', '{"target_id":"1","user_id":null,"user":{"user_id":null,"photo":"","displayName":""},"content":"Comment n+4","likes":null,"dislikes":null,"comments":[]}', 'POST', true);
        
        //assert created
        $this->assertTrue((bool)preg_match('~HTTP/1\.1 2\d\d~m', $result));
        
        $this->assertTrue((bool)preg_match('~"success":\s?"?true"?~m', $result));
    }
        
}
