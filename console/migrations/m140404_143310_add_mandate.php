<?php

class m140404_143310_add_mandate extends EDbMigration
{
	public function safeUp()
	{
            $this->createTable('mandate', array(
                'id' => 'pk',
                'election_id' => 'int(11) not null',
                'candidate_id'=> 'int(11) not null', 
                'name'        => 'varchar(1000) not null',
                'submiting_ts' => 'timestamp not null default "0000-00-00 00:00:00"',
                'expiration_ts'=> 'timestamp not null default "0000-00-00 00:00:00"',
                'validity'     => 'tinyint not null',
                'votes_count'  => 'int(11) not null',
                'status'       => 'tinyint not null default 0'
            ));
            
            $this->createIndex('ix_mandate_submiting_ts', 'mandate', 'submiting_ts');
            $this->createIndex('ix_mandate_expiration_ts', 'mandate', 'expiration_ts');
            $this->createIndex('ix_mandate_name', 'mandate', 'name');
            
            $this->addForeignKey('fk_mandate_election_id', 'mandate', 'election_id', 'election', 'id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_mandate_candidate_id', 'mandate', 'candidate_id', 'candidate', 'id', 'CASCADE', 'NO ACTION');
	}

	public function safeDown()
	{
            $this->dropTable('mandate');
	}
}