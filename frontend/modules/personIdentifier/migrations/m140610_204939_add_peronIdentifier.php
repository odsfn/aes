<?php

class m140610_204939_add_peronIdentifier extends EDbMigration
{
	public function up()
	{
            $this->createTable('personIdentifier', array(
                'id' => 'pk',
                'profile_id' => 'int(11) not null',
                'status' => 'tinyint unsigned not null',
                'last_update_ts' => 'timestamp not null default "0000-00-00"',
                'type' => 'varchar(256) not null',
                'image' => 'varchar(512) not null',
                'data' => 'text'
            ));
            
            $this->addForeignKey('fk_personIdentifier_profile_id', 'personIdentifier', 'profile_id', 'user_profile', 'user_id');
	}

	public function down()
	{
            $this->dropTable('personIdentifier');
	}
}