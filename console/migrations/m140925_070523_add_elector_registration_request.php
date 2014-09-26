<?php

class m140925_070523_add_elector_registration_request extends EDbMigration
{
	public function up()
	{
            $this->createTable('elector_registration_request', array(
                'id' => 'pk',
                'created_ts' => 'timestamp not null default "0000-00-00"',
                //User's id who asked registration. It can be equal to user_id 
                //in the case if user asked participation for himself. It can be
                //equal to one of those users who has election_administration 
                //rights. In the case if admin send invitation and user should 
                //response to it
                'initiator_id' => 'int(11) default null',    
                'election_id' => 'int(11) not null',
                //this user will be elector
                'user_id' => 'int(11) not null',
                //Here will be stored serialized extra data as list of VoterGroups,
                //some comments to status change etc...
                'data' => 'text default null',
                'status' => 'tinyint unsigned default 0'
            ));
            
            $this->addForeignKey(
                'fk_elector_registration_request_initiator', 
                'elector_registration_request', 'initiator_id', 'user_profile', 
                'user_id', 'CASCADE', 'NO ACTION'
            );
            
            $this->addForeignKey(
                'fk_elector_registration_request_election', 
                'elector_registration_request', 'election_id', 'election', 
                'id', 'CASCADE', 'NO ACTION'
            );
            
            $this->addForeignKey(
                'fk_elector_registration_request_user', 
                'elector_registration_request', 'user_id', 'user_profile', 
                'user_id', 'CASCADE', 'NO ACTION'
            );
	}

	public function down()
	{
            $this->dropTable('elector_registration_request');
	}
}