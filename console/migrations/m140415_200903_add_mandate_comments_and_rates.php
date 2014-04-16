<?php
Yii::import('console.helpers.RateableDbManagerHelper');
Yii::import('console.helpers.CommentableDbManagerHelper');

class m140415_200903_add_mandate_comments_and_rates extends EDbMigration
{
	public function safeUp()
	{
            RateableDbManagerHelper::createTables('mandate', $this);
            CommentableDbManagerHelper::createTables('mandate', $this);
	}

	public function safeDown()
	{
            RateableDbManagerHelper::dropTables('mandate', $this);
            CommentableDbManagerHelper::dropTables('mandate', $this);
	}
}