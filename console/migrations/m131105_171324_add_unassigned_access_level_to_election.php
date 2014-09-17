<?php

Yii::import('common.models.Election');

class m131105_171324_add_unassigned_access_level_to_election extends EDbMigration
{
	public function up()
	{
            $this->addColumn('election', 'unassigned_access_level', 'TINYINT NOT NULL DEFAULT ' . Election::UNASSIGNED_CAN_POST);
	}

	public function down()
	{
            $this->dropColumn('election', 'unassigned_access_level');
	}
}