<?php

class m131029_172859_add_comment_authItems extends EDbMigration
{
	public function up()
	{
            $this->execute(file_get_contents(Yii::getPathOfAlias('system.web.auth') . '/schema-mysql.sql'));
            
            $auth = Yii::app()->authManager;
            
            $auth->createOperation('createComment');
            $auth->createOperation('readComment');
            $auth->createOperation('updateComment');
            $auth->createOperation('deleteComment');
            
            $task = $auth->createTask('manageOwnComment', '', 'return ($params["userId"]==$params["comment"]->user_id);');
            $task->addChild('updateComment');
            $task->addChild('deleteComment');
            
            $role = $auth->createRole('commentReader', '', 'return Yii::app()->user->isGuest;');
            $role->addChild('readComment');
            
            $role = $auth->createRole('commentor', '', 'return !Yii::app()->user->isGuest;');
            $role->addChild('commentReader');
            $role->addChild('manageOwnComment');
            $role->addChild('createComment');
            
            $role = $auth->createRole('commentModerator', '');
            $role->addChild('commentor');
            $role->addChild('deleteComment');
            
	}

	public function down()
	{
            $auth = Yii::app()->authManager;
            
            $authItems = array(
                'createComment', 'readComment', 'updateComment', 'deleteComment',
                'manageOwnComment', 'commentReader', 'commentor', 'commentModerator'
            );
            
            foreach ($authItems as $item)
                $auth->removeAuthItem($item);
            
            $this->dropTable('AuthAssignment');
            $this->dropTable('AuthItemChild');
            $this->dropTable('AuthItem');
	}
}