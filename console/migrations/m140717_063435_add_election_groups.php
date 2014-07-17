<?php

class m140717_063435_add_election_groups extends EDbMigration
{
    public function up()
    {
        $this->addColumn('election', 'voter_group_restriction', 
                'tinyint unsigned not null default 0');
        
        $this->createTable('voter_group', array(
            'id' => 'pk',
            'name' => 'varchar(512) not null',
            'type' => 'tinyint unsigned not null default 0',
            'status' => 'tinyint unsigned not null default 1',
            'user_id' => 'int(11) not null',
            'created_ts' => 'timestamp not null',
        ));
        $this->createIndex('ix_voter_group_name', 'voter_group', 'name');
        $this->addForeignKey('fk_voter_group_user_id', 'voter_group', 
                'user_id', 'user', 'id', 'CASCADE', 'NO ACTION');
        
        $this->createTable('voter_group_member', array(
            'id' => 'pk',
            'voter_group_id' => 'int(11) not null',
            'user_id' => 'int(11) not null',
            'created_ts' => 'timestamp not null'
        ));
        $this->addForeignKey('fk_voter_group_member_voter_group_id', 
                'voter_group_member', 'voter_group_id', 'voter_group', 'id',
                'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_voter_group_member_user_id', 'voter_group_member', 
                'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
        
        $this->createTable('election_group', array(
            'id' => 'pk',
            'election_id' => 'int(11) not null',
            'voter_group_id' => 'int(11) not null'
        ));
        $this->addForeignKey('fk_election_group_election_id', 'election_group',
                'election_id', 'election', 'id', 'CASCADE', 'NO ACTION');
        $this->addForeignKey('fk_election_group_voter_group_id', 'election_group',
                'voter_group_id', 'voter_group', 'id', 'CASCADE', 'NO ACTION');
    }

    public function down()
    {
        $this->dropColumn('election', 'voter_group_restriction');
        
        $this->dropTable('election_group');
        $this->dropTable('voter_group_member');
        $this->dropTable('voter_group');
    }
}