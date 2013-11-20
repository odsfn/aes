<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class ElectionAccessCheckTest extends CDbTestCase {
    
    protected $fixtures = array(
        'election' => 'Election',
        'election_auth_assignment' => ':election_auth_assignment',
        'AuthAssignment'   => ':AuthAssignment'
    );
    
        
    public function testPostCheckAccess() {
        
        $election1 = Election::model()->findByPk(1);
        $election2 = Election::model()->findByPk(2);
        
        //Moderator ( user 3 ) can moderate comments of Election 1
        
        $result = Yii::app()->getAuthManager()->checkAccess('election_creator',3,array('election' => $election1));
        $this->assertTrue($result);
        
        $result = Yii::app()->getAuthManager()->checkAccess('deletePost',3,array('election' => $election1));
        $this->assertTrue($result);
        
        //Moderator ( user 4 ) also can moderate comments of Election 1
        
        $result = Yii::app()->getAuthManager()->checkAccess('election_admin',4,array('election' => $election1));
        $this->assertTrue($result);
        
        $result = Yii::app()->getAuthManager()->checkAccess('deletePost',4,array('election' => $election1));
        $this->assertTrue($result);        
        
        //Moderator ( user 1 ) can moderate comments of Election 2
        
        $result = Yii::app()->getAuthManager()->checkAccess('election_creator',1,array('election' => $election2));
        $this->assertTrue($result);
        
        $result = Yii::app()->getAuthManager()->checkAccess('deletePost',1,array('election' => $election2));
        $this->assertTrue($result);
        
        //Moderator ( user 4 ) also can moderate comments of Election 2
        
        $result = Yii::app()->getAuthManager()->checkAccess('election_admin',4,array('election' => $election2));
        $this->assertTrue($result);
        
        $result = Yii::app()->getAuthManager()->checkAccess('deletePost',4,array('election' => $election2));
        $this->assertTrue($result);          
        
        //Moderator ( user 1 ) cant moderate comments of Election 1     
        
        $result = Yii::app()->getAuthManager()->checkAccess('election_creator',1,array('election' => $election1));
        $this->assertFalse($result);
        
        $result = Yii::app()->getAuthManager()->checkAccess('deletePost',1,array('election' => $election1));
        $this->assertFalse($result);
        
        //Moderator ( user 3 ) cant moderate comments of Election 2          
        
        $result = Yii::app()->getAuthManager()->checkAccess('election_creator',3,array('election' => $election2));
        $this->assertFalse($result);
        
        $result = Yii::app()->getAuthManager()->checkAccess('deletePost',3,array('election' => $election2));
        $this->assertFalse($result);
        
    }
    
    public function testCheckAccessUpdateOwn() {
        
        $user = Yii::app()->user;
        
        $userMock = $this->getMock('CWebUser', array('getId', 'getIsGuest', 'init'));
        
        $userMock->expects($this->any())
                ->method('getId')
                ->will($this->returnValue(1));
        
        $userMock->expects($this->any())
                ->method('getIsGuest')
                ->will($this->returnValue(false));
        
        Yii::app()->setComponent('user', $userMock, false);
        
        $post = array(
            'user_id' => 1
        );
        
        $result = Yii::app()->getAuthManager()->checkAccess('updatePost', 1, array(
            'election' => $comment->target,
            'post'  => (object)$post
        ));
        
        $this->assertTrue($result);
        
        Yii::app()->setComponent('user', $user, false);
    }
    
    public function testRestrictCreatePostForNotParticipants() {
        
        $auth = Yii::app()->authManager;
        
        $user = Yii::app()->user;
        
        $election1 = Election::model()->findByPk(1);
        
        //user 5 is not a participant
        $user->id = 5;
        $this->assertFalse($auth->checkAccess('createPost', 5, array(
            'disabledRoles' => array('poster'),
            'election' => $election1
        )));
        
        // user 6 is participant
        $user->id = 6;
        
        $election1->assignRoleToUser($user->id, 'election_participant');
        
        $this->assertTrue($auth->checkAccess('createPost', 6, array(
            'disabledRoles' => array('poster'),
            'election' => $election1
        )));
        
        // user 4 is moderator
        $user->id = 4;
        
        $this->assertTrue($auth->checkAccess('createPost', 4, array(
            'disabledRoles' => array('poster'),
            'election' => $election1
        )));
        
        // user ??? is creator ?
    }
    
}
