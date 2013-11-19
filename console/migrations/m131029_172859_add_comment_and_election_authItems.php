<?php

class m131029_172859_add_comment_and_election_authItems extends EDbMigration
{
	public function up()
	{   
            $auth = Yii::app()->authManager;
            
            $auth->createOperation('createComment');
            $auth->createOperation('readComment');
            $auth->createOperation('updateComment');
            $auth->createOperation('deleteComment');
            
            $task = $auth->createTask('manageOwnComment', '', 'return ($params["userId"]==$params["comment"]->user_id);');
            $task->addChild('updateComment');
            $task->addChild('deleteComment');
            
            $task = $auth->createTask('commentation');
            $task->addChild('readComment');
            $task->addChild('manageOwnComment');
            $task->addChild('createComment');            
            
            $role = $auth->createRole('commentReader', '', 'return (!in_array("commentReader", $params["disabledRoles"]));');
            $role->addChild('readComment');
            
            $role = $auth->createRole('commentor', '', 'return (!in_array("commentor", $params["disabledRoles"]) && !Yii::app()->user->isGuest);');
            $role->addChild('commentation');
            
            // Election RBAC hierarchy     
            
            $role = $auth->createRole('election_participant', '', 'return (isset($params["election"]) && $params["election"]->checkUserInRole($params["userId"], "election_participant"));');
            $role->addChild('commentation');
            
            $role = $auth->createRole('election_commentModerator', '', 'return (isset($params["election"]) && $params["election"]->checkUserInRole($params["userId"], "election_commentModerator"));');
            $role->addChild('commentation');
            $role->addChild('deleteComment');
            
            $auth->createTask('election_manage');
            
            $task = $auth->createTask('election_administration');
            $task->addChild('commentation');
            $task->addChild('deleteComment');
            $task->addChild('election_manage');
            
            $role = $auth->createRole('election_admin', '', 'return (isset($params["election"]) && $params["election"]->checkUserInRole($params["userId"], "election_admin"));');
            $role->addChild('election_administration');
            
            $auth->createOperation('election_addAdmin');
            $auth->createOperation('election_deleteAdmin');
            
            $task = $auth->createTask('election_manageAdmins');
            $task->addChild('election_addAdmin');
            $task->addChild('election_deleteAdmin');
            
            $role = $auth->createRole('election_creator', '', 'return (isset($params["election"]) && $params["election"]->user_id == $params["userId"]);');
            $role->addChild('election_administration');
            $role->addChild('election_manageAdmins');
	}

	public function down()
	{   
            $auth = Yii::app()->authManager;
            
            $authItems = array(
                'election_creator','election_manageAdmins','election_addAdmin',
                'election_deleteAdmin','election_admin','election_administration',
                'election_manage','election_commentModerator','election_participant',
                'commentor','commentReader','commentation','manageOwnComment',
                'createComment','readComment','updateComment','deleteComment'
            );
            
            foreach ($authItems as $item)
                $auth->removeAuthItem($item);
	}
}