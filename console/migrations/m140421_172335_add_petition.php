<?php
Yii::import('console.helpers.*');


class m140421_172335_add_petition extends EDbMigration
{

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
            $this->createTable('petition', array(
                'id' => 'pk',
                'title'   => 'varchar(1024) not null',
                'content' => 'text not null',
                'mandate_id' => 'int(11) not null',
                'creator_id' => 'int(11) not null',
                'created_ts' => 'timestamp not null'
            ));
            
            $this->createIndex('ix_petition_title', 'petition', 'title');
            $this->addForeignKey('fk_petition_mandate', 'petition', 'mandate_id', 'mandate', 'id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk_petition_creator', 'petition', 'creator_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
            
            RateableDbManagerHelper::createTables('petition', $this);
            
            CommentableDbManagerHelper::createTables('petition', $this);
	}

	public function safeDown()
	{
            CommentableDbManagerHelper::dropTables('petition', $this);
            RateableDbManagerHelper::dropTables('petition', $this);
            
            $this->dropTable('petition');
	}

}