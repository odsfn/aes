<?php

Yii::import('console.helpers.*');

class m130904_094231_add_post extends EDbMigration
{
	public function up()
	{
            //This is Base Parent Table to provide polymorphic associations for 
            //post and entities that can be the target of posting. Each such entity
            //will have target_id as foreign key ( which can be also a primary key )
            //to the target.target_id
            //
            //See http://www.slideshare.net/billkarwin/practical-object-oriented-models-in-sql 36 slide for
            //more details about this pattern
            $this->createTable('target', array(
                'target_id' => 'pk',
                'target_type' => 'VARCHAR(64) NOT NULL'
            ));
            
            $this->createIndex('ix_target_target_type', 'target', 'target_type');
            
            $this->createTable('post', array(
                'id' => 'pk',
                'target_id'     => 'INT(11) NOT NULL',
                'user_id' => 'int(11) NOT NULL',
                'reply_to'   => 'int(11) NULL',                                 //Post if NULL, else is comment
                'content' => 'TEXT NOT NULL',                                   
                'created_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"',      
                'last_update_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"'   
            ));
            
            $this->addForeignKey('fk_post_target_id', 'post', 'target_id', 'target', 'target_id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_post_user_id', 'post', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_post_reply_to', 'post', 'reply_to', 'post', 'id', 'CASCADE', 'NO ACTION');
            
            RateableDbManagerHelper::createTables('post', $this);
            
            $this->addColumn('user_profile', 'target_id', 'INT(11) NOT NULL');
            $this->addForeignKey('fk_profile_target_id', 'user_profile', 'target_id', 'target', 'target_id', 'CASCADE', 'NO ACTION');
            
            //create auth items
            $auth = Yii::app()->authManager;
            
            $auth->createOperation('createPost');
            $auth->createOperation('readPost');
            $auth->createOperation('updatePost');
            $auth->createOperation('deletePost');
            
            $task = $auth->createTask('manageOwnPost', '', 'return (isset($params["post"]) && $params["post"]->user_id == $params["userId"]);');
            $task->addChild('updatePost');
            $task->addChild('deletePost');
            
            $task = $auth->createTask('posting');
            $task->addChild('readPost');
            $task->addChild('manageOwnPost');
            $task->addChild('createPost');            
            
            $task = $auth->createTask('postsModeration');
            $task->addChild('posting');
            $task->addChild('deletePost');
            
            $role = $auth->createRole('postReader', '', 'return (!in_array("postReader", $params["disabledRoles"]));');
            $role->addChild('readPost');
            
            $role = $auth->createRole('poster', '', 'return (!in_array("poster", $params["disabledRoles"]) && !Yii::app()->user->isGuest);');
            $role->addChild('posting');            
            
            
            $role = $auth->createRole('userPageOwner', '', 'return ( isset($params["profile"]) && $params["profile"]->user_id == $params["userId"] );');
            $role->addChild('deletePost');
            
            $authenticatedRole = $auth->getAuthItem('authenticated');
            $authenticatedRole->addChild('manageOwnPost');
            $authenticatedRole->addChild('userPageOwner');
            $authenticatedRole->addChild('postReader');
            $authenticatedRole->addChild('poster');
            
            $guestRole = $auth->getAuthItem('guest');
            $guestRole->addChild('postReader');
	}

	public function down()
	{
            $auth = Yii::app()->authManager;
            
            $authItems = array('postsModeration','poster','postReader','posting',
                'userPageOwner', 'manageOwnPost', 'createPost', 
                'readPost', 'updatePost', 'deletePost');
            
            foreach ($authItems as $item)
                $auth->removeAuthItem($item);
            
            $this->dropForeignKey('fk_profile_target_id', 'user_profile');
            $this->dropColumn('user_profile', 'target_id');
            RateableDbManagerHelper::dropTables('post', $this);
            $this->dropTable('post');
            $this->dropTable('target');
	}
}