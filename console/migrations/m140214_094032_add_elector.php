<?php

class m140214_094032_add_elector extends EDbMigration
{
	public function safeUp()
	{
            $this->createTable('elector', array(
                'id' => 'pk',
                'election_id' => 'int(11) not null',
                'user_id'  => 'int(11) not null'
            ));
            
            $this->createIndex('ux_elector_election_user_id', 'elector', 'election_id, user_id', true);
            $this->addForeignKey('fk_elector_election', 'elector', 'election_id', 'election', 'id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_elector_profile', 'elector', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            
	}

	public function safeDown()
	{
            $this->dropTable('elector');
	}
}