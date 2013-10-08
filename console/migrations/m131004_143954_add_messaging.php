<?php

class m131004_143954_add_messaging extends EDbMigration
{
	public function up()
	{
            $this->createTable('conversation', array(
                'id'    => 'pk',
                'title' => 'VARCHAR(256) NOT NULL DEFAULT ""',
                'created_ts' => 'TIMESTAMP NOT NULL DEFAULT "0000-00-00"',
                'initiator_id' => 'INT(11) NOT NULL'
            ));
            
            $this->addForeignKey('fk_initiator_id', 'conversation', 'initiator_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            $this->createIndex('ix_created_ts', 'conversation', 'created_ts');
            
            $this->createTable('conversation_participant', array(
                'id'              => 'pk',
                'conversation_id' => 'INT(11) NOT NULL',
                'user_id'         => 'INT(11) NOT NULL',
                'last_view_ts'    => 'TIMESTAMP NOT NULL DEFAULT "0000-00-00"'
            ));
            
            $this->createIndex('ux_conversation_id_user_id', 'conversation_participant', 'conversation_id, user_id', true);
            $this->addForeignKey('fk_conversation_id', 'conversation_participant', 'conversation_id', 'conversation', 'id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_user_id', 'conversation_participant', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            
            $this->createTable('message', array(
                'id'                 => 'pk',
                'conversation_id'    => 'INT(11) NOT NULL',
                'user_id'            => 'INT(11) NOT NULL',
                'created_ts'         => 'TIMESTAMP NOT NULL DEFAULT "0000-00-00"',
                'text'               => 'TEXT'
            ));
            
            $this->addForeignKey('fk_message_conversation_id', 'message', 'conversation_id', 'conversation', 'id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_message_user_id', 'message', 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
	}

	public function down()
	{
            $this->dropTable('message');
            $this->dropTable('conversation_participant');
            $this->dropTable('conversation');
	}
}