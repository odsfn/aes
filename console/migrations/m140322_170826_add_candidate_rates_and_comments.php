<?php
Yii::import('console.helpers.RateableDbManagerHelper');
Yii::import('console.helpers.CommentableDbManagerHelper');

class m140322_170826_add_candidate_rates_and_comments extends EDbMigration
{
    public function safeUp()
    {
        RateableDbManagerHelper::createTables('candidate', $this);
        CommentableDbManagerHelper::createTables('candidate', $this);
    }

    public function safeDown()
    {
        RateableDbManagerHelper::dropTables('candidate', $this);
        CommentableDbManagerHelper::dropTables('candidate', $this);
    }

}