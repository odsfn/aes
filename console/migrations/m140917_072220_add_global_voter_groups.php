<?php

class m140917_072220_add_global_voter_groups extends EDbMigration
{
    public function up()
    {
        $this->addColumn('voter_group', 'election_id', 'int(11) default NULL');
        $this->addForeignKey('fk_voter_group_election', 'voter_group', 
                'election_id', 'election', 'id', 'CASCADE', 'NO ACTION');
    }

    public function down()
    {
        $this->dropForeignKey('fk_voter_group_election', 'voter_group');
        $this->dropColumn('voter_group', 'election_id');
    }
}