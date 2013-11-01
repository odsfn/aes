<?php

Yii::import('console.components.RateableDbManagerHelper');
Yii::import('console.components.CommentableDbManagerHelper');

class m131021_084053_add_comments_and_rates extends EDbMigration
{
	public function up() 
        {
            CommentableDbManagerHelper::createTables('election', $this);
	}

	public function down()
	{
            CommentableDbManagerHelper::dropTables('election', $this);
	}
}