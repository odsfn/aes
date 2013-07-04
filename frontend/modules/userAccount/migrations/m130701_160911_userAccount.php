<?php
/**
 * Sets up tables for userAccount module
 * 
 * @TODO: 
 * - move it to the module
 * - make customizable ( table names , prefixes )
 * - leave only common fields in profile
 * - provide storage for profile privacy filter
 * - provide storage for profile's notifications settings
 */
class m130701_160911_userAccount extends CDbMigration
{
	public function up()
	{
	    $this->createTable('user', array(
		'id'=>'pk',
		'login'=>'varchar(64) null',
		"password" => "varchar(128) NOT NULL DEFAULT ''",
		"created_ts" => "timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'",
		"last_visit_ts" => "timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'",
		"superuser" => "int(1) NOT NULL DEFAULT 0",
		"status" => "int(1) NOT NULL DEFAULT 0"
	    ));
	    $this->createIndex("i_user_login", "user", "login");
	    
	    $this->createTable('user_identity', array(
		"id"=>'pk',
		"user_id"=>"int(11) NOT NULL",
		"identity"=>"varchar(128) NOT NULL",
		"type"=>"varchar(128) NOT NULL",
		"status" => "int(1) NOT NULL DEFAULT 0"
	    ));
	    $this->addForeignKey("fk_user_identity_user_id", "user_identity", "user_id", "user", "id", "CASCADE", "NO ACTION");
	    
	    $this->createTable('user_profile', array(
		"user_id"=>"int(11) NOT NULL PRIMARY KEY",
		"first_name"=>"varchar(128) NOT NULL DEFAULT ''",
		"last_name"=>"varchar(128) NOT NULL DEFAULT ''",
		"birth_place"=>"varchar(128) NOT NULL DEFAULT ''",
		"birth_day"=>"DATE",
		"gender"=>"int(1) DEFAULT NULL",
		"mobile_phone"=>"varchar(18) NOT NULL DEFAULT ''",
		"email"=>"varchar(128) NOT NULL DEFAULT ''"
	    ));
	    $this->addForeignKey("fk_user_profile_user_id", "user_profile", "user_id", "user", "id", "CASCADE", "NO ACTION");
	    $this->createIndex("i_first_name", "user_profile", "first_name");
	    $this->createIndex("i_last_name", "user_profile", "last_name");
	    $this->createIndex("i_mobile_phone", "user_profile", "mobile_phone");
	    
	    $this->createTable('user_identity_confirmation', array(
		"user_identity_id"=>"int(11) NOT NULL PRIMARY KEY",
		"type"=>"int(1) NOT NULL DEFAULT 0",	//i.e. (email|phone)_(confirmation|password_reset), secure_authentication (by phone) etc...
		"key"=>"varchar(128) NOT NULL",
		"sent_ts"=>"timestamp NOT NULL DEFAULT '0000-00-00'",
		"status" => "int(1) NOT NULL DEFAULT 0"
	    ));
	    $this->addForeignKey("fk_uic_u_identity_id", "user_identity_confirmation", "user_identity_id", "user_identity", "id", "CASCADE", "NO ACTION");
	    $this->createIndex("i_user_identity_confirmation_key", "user_identity_confirmation", "key", true);
	}

	public function down()
	{
	    $this->dropTable('user_identity_confirmation');
	    $this->dropTable('user_identity');
	    $this->dropTable('user_profile');
	    $this->dropTable('user');
	}
}