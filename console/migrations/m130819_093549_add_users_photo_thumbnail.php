<?php

class m130819_093549_add_users_photo_thumbnail extends EDbMigration
{
	public function up()
	{
	    $this->addColumn('user_profile', 'photo_thmbnl_64', 'varchar(256) NULL DEFAULT NULL');
	}

	public function down()
	{
	    $this->dropColumn('user_profile', 'photo_thmbnl_64');
        }
}