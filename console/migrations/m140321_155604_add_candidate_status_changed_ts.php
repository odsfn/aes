<?php

class m140321_155604_add_candidate_status_changed_ts extends EDbMigration
{
	public function safeUp()
	{
            $this->addColumn('candidate', 'status_changed_ts', 'TIMESTAMP DEFAULT "0000-00-00 00:00:00"');
            $this->createIndex('ix_candidate_status_changed_ts', 'candidate', 'status_changed_ts');
	}

	public function safeDown()
	{
            $this->dropColumn('candidate', 'status_changed_ts');
	}

}