<?php
Yii::import('console.helpers.RateableDbManagerHelper');
Yii::import('console.helpers.CommentableDbManagerHelper');

class m140329_200625_add_vote_comment_and_rate extends EDbMigration
{
    public function safeUp()
    {
        RateableDbManagerHelper::createTables('vote', $this);
        CommentableDbManagerHelper::createTables('vote', $this);
    }

    public function safeDown()
    {
        RateableDbManagerHelper::dropTables('vote', $this);
        CommentableDbManagerHelper::dropTables('vote', $this);
    }
}