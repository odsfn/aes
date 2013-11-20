<?php

class m130800_033720_add_auth_tables extends EDbMigration
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
            $auth->createRole('authenticated', '', 'return (!empty($params["userId"]));');
            $auth->createRole('guest', '', 'return (empty($params["userId"]));');
	}

	public function down()
	{   
            $this->dropTable('AuthAssignment');
            $this->dropTable('AuthItemChild');
            $this->dropTable('AuthItem');
	}
}