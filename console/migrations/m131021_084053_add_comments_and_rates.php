<?php

Yii::import('console.components.RateableDbManagerHelper');
Yii::import('console.components.CommentableDbManagerHelper');

class m131021_084053_add_comments_and_rates extends EDbMigration
{
	public function up() {
            
            /* Variant one
             *
             * Drawbacks: 
             * 1. Need to add FkBehavior to each target model
            
            $this->createTable('comment', array(
                'id' => 'pk',
                'target_id'     => 'INT(11) NOT NULL',
                'target_type'   => 'VARCHAR(16) NOT NULL',  // group, page, election
                'user_id' => 'int(11) NOT NULL',
                'content' => 'TEXT NOT NULL',
                'created_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"',
                'last_update_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"'
            ));
            
            $this->addForeignKey('fk_comment_user_id', 'post', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            $this->createIndex('ix_comment_target', 'comment', 'target_id, target_type', false);
            
            $this->createTable('rate', array(
                'id' => 'pk',
                'user_id' => 'int(11) NOT NULL',
                'target_id' => 'int(11) NOT NULL',
                'target_type'   => 'VARCHAR(16) NOT NULL',  // group, page, election
                'created_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"',
                'score' => 'tinyint NOT NULL'
            ));
            
            $this->addForeignKey('fk_rate_user_id', 'rate', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            $this->createIndex('ux_comment_rate_user_id_target_id_target_type', 'rate', 'user_id, target_id, target_type', $unique = true);
             
            */
            
            
            CommentableDbManagerHelper::createTables('election', $this);
            
           
	}

	public function down()
	{
            CommentableDbManagerHelper::dropTables('election', $this);
	}
}