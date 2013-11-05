<?php

class m131029_172859_add_comment_authItems extends EDbMigration
{
	public function up()
	{
            $this->execute(file_get_contents(Yii::getPathOfAlias('system.web.auth') . '/schema-mysql.sql'));
            
            $this->dropTable('AuthAssignment');
            
            $this->execute(
                    "create table `AuthAssignment`
                    (
                       `id`                   int(11) not null AUTO_INCREMENT,  
                       `itemname`             varchar(64) not null,
                       `userid`               int(11) not null,
                       `bizrule`              text,
                       `data`                 text,
                       primary key (`id`),
                       foreign key (`itemname`) references `AuthItem` (`name`) on delete cascade on update cascade
                    ) engine InnoDB;"
            );
            
            $this->createIndex('ux_AuthAssignment_itemname_userid', 'AuthAssignment', 'itemname,userid', true);
            $this->addForeignKey('fk_AuthAssignment_userid', 'AuthAssignment', 'userid', 'user', 'id', 'CASCADE', 'NO ACTION');
            
            $auth = Yii::app()->authManager;
            
            $auth->createOperation('createComment');
            $auth->createOperation('readComment');
            $auth->createOperation('updateComment');
            $auth->createOperation('deleteComment');
            
            $task = $auth->createTask('manageOwnComment', '', 'return ($params["userId"]==$params["comment"]->user_id);');
            $task->addChild('updateComment');
            $task->addChild('deleteComment');
            
            $role = $auth->createRole('commentReader', '', 'return (!in_array("commentReader", $params["disabledRoles"]));');
            $role->addChild('readComment');
            
            $role = $auth->createRole('commentor', '', 'return (!in_array("commentor", $params["disabledRoles"]) && !Yii::app()->user->isGuest);');
            $role->addChild('readComment');
            $role->addChild('manageOwnComment');
            $role->addChild('createComment');
            
            $role = $auth->createRole('election_commentor');
            $role->addChild('readComment');
            $role->addChild('manageOwnComment');
            $role->addChild('createComment');            
            
            $role = $auth->createRole('election_commentModerator', '', 'return (isset($params["election"]) && $params["election"]->checkUserInRole($params["userId"], "election_commentModerator"));');
            $role->addChild('election_commentor');
            $role->addChild('deleteComment');
            
            $role = $auth->createRole('election_participant', '', 'return (isset($params["election"]) && $params["election"]->checkUserInRole($params["userId"], "election_participant"));');
            $role->addChild('election_commentor');
	}

	public function down()
	{   
            $this->dropTable('AuthAssignment');
            $this->dropTable('AuthItemChild');
            $this->dropTable('AuthItem');
	}
}