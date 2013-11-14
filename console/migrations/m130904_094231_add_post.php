<?php

class m130904_094231_add_post extends EDbMigration
{
	public function up()
	{
            //This is Base Parent Table to provide polymorphic associations for 
            //post and entities that can be the target of posting. Each such entity
            //will have target_id as foreign key ( which can be also a primary key )
            //to the target.target_id
            //
            //See http://www.slideshare.net/billkarwin/practical-object-oriented-models-in-sql 36 slide for
            //more details about this pattern
            $this->createTable('target', array(
                'target_id' => 'pk',
                'target_type' => 'VARCHAR(64) NOT NULL'
            ));
            
            $this->createIndex('ix_target_target_type', 'target', 'target_type');
            
            $this->createTable('post', array(
                'id' => 'pk',
                'target_id'     => 'INT(11) NOT NULL',
                'user_id' => 'int(11) NOT NULL',
                'reply_to'   => 'int(11) NULL',                                 //Post if NULL, else is comment
                'content' => 'TEXT NOT NULL',                                   
                'created_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"',      
                'last_update_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"'   
            ));
            
            $this->addForeignKey('fk_post_target_id', 'post', 'target_id', 'target', 'target_id', 'CASCADE', 'NO ACTION');
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
            
            $this->addColumn('user_profile', 'target_id', 'INT(11) NOT NULL');
            $this->addForeignKey('fk_profile_target_id', 'user_profile', 'target_id', 'target', 'target_id', 'CASCADE', 'NO ACTION');
	}

	public function down()
	{
            $this->dropColumn('profile', 'target_id');
            $this->dropTable('post_rate');
            $this->dropTable('post');
	}
}