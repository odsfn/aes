<?php

class m130904_094231_add_post extends EDbMigration
{
	public function up()
	{
            $this->createTable('post', array(
                'id' => 'pk',
                'user_id' => 'int(11) NOT NULL',
                'reply_to'   => 'int(11) NULL',
                'content' => 'TEXT NOT NULL',
                'created_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"',
                'last_update_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"'
            ));
            
            $this->addForeignKey('fk_post_user_id', 'post', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_post_reply_to', 'post', 'reply_to', 'post', 'id', 'CASCADE', 'NO ACTION');
            
            $this->createTable('post_rate', array(
                'id' => 'pk',
                'user_id' => 'int(11) NOT NULL',
                'post_id' => 'int(11) NOT NULL',
                'created_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"',
                'score' => 'tinyint NOT NULL'
            ));
            
            $this->addForeignKey('fk_post_rate_user_id', 'post_rate', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_post_post_id', 'post_rate', 'post_id', 'post', 'id', 'CASCADE', 'NO ACTION');
            $this->createIndex('ux_post_rate_user_id_post_id', 'post_rate', 'user_id, post_id', $unique = true);
	}

	public function down()
	{
            $this->dropTable('post_rate');
            $this->dropTable('post');
	}
}