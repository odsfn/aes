<?php

class m130808_144831_add_users_photo extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('user_profile', 'photo', 'varchar(256) NULL DEFAULT NULL');
	}

	public function down()
	{
	    $this->dropColumn('user_profile', 'photo');
	}
}