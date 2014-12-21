<?php

class m141220_194123_make_galleryItems_commentable extends EDbMigration
{
    public function up()
    {
        CommentableDbManagerHelper::createTables('file', $this);
        CommentableDbManagerHelper::createTables('video', $this);
    }

    public function down()
    {
        CommentableDbManagerHelper::dropTables('file', $this);
        CommentableDbManagerHelper::dropTables('video', $this);
    }
}