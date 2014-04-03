<?php

class m140403_161910_add_commentation_auth_fixes extends EDbMigration
{

	public function safeUp()
	{
            $auth = Yii::app()->authManager;
            
            $role = $auth->getAuthItem('election_participant');
            $role->addChild('commentation');
            
            $task = $auth->createTask('commentModeration');
            $task->addChild('deleteComment');
            
            $role = $auth->createRole('commentModerator', '', 'return (isset($params["target"]) && $params["target"]->checkUserInRole($params["userId"], "commentModerator"));');
            $role->addChild('commentation');
            $role->addChild('commentModeration');
            
            $task = $auth->getAuthItem('election_administration');
//            $task->addChild('commentation');
//            $task->addChild('deleteComment');
            $task->addChild('commentModeration');
        }

	public function safeDown()
	{
            $auth = Yii::app()->authManager;
            $auth->removeAuthItem('commentModeration');
            $auth->removeAuthItem('commentModerator');
            
            
//            $auth->removeItemChild('election_participant', 'commentation');
            
//            $auth->removeItemChild('election_administration', 'commentation');
//            $auth->removeItemChild('election_administration', 'deleteComment');
            
//            $auth->removeItemChild('election_administration', 'commentModeration');
        }

}