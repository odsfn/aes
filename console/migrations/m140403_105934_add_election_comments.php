<?php
Yii::import('console.helpers.RateableDbManagerHelper');
Yii::import('console.helpers.CommentableDbManagerHelper');

class m140403_105934_add_election_comments extends EDbMigration
{
    public function safeUp()
    {
        CommentableDbManagerHelper::createTables('election', $this);
    }

    public function safeDown()
    {
        CommentableDbManagerHelper::dropTables('election', $this);
    }
}