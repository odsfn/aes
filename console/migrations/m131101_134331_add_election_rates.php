<?php

Yii::import('console.helpers.RateableDbManagerHelper');

class m131101_134331_add_election_rates extends EDbMigration
{
	public function up()
	{
            RateableDbManagerHelper::createTables('election', $this);
	}

	public function down()
	{
            RateableDbManagerHelper::dropTables('election', $this);
	}
}